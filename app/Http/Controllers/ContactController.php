<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Store or update a contact from the Live Chat UI.
     */
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $request->input('phone'));
        // Remove + if present for consistency, though based on LiveChatController, we might just store what we have.
        // The LiveChatController adds '+' in some places, but let's stick to the raw digits to match whatsapp logs.
        $phoneDigits = ltrim($phone, '+');

        try {
            $contact = Contact::updateOrCreate(
                ['phone' => $phoneDigits],
                [
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'notes' => $request->input('notes'),
                    'is_manually_saved' => true,
                    'user_id' => Auth::id() // Associate with current user if possible
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Contact saved successfully.',
                'data' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'phone' => $contact->phone,
                    'email' => $contact->email,
                    'notes' => $contact->notes,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving contact: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save contact.'
            ], 500);
        }
    }

    /**
     * Display a listing of the contacts.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Contact::query();

        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
                $query->whereIn('user_id', $userIds);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('contacts.index', compact('contacts'));
    }

    /**
     * Store a newly created contact.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $request->input('phone'));
        $phoneDigits = ltrim($phone, '+');

        $exists = Contact::where('phone', $phoneDigits)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'A contact with this phone number already exists.'], 400);
        }

        try {
            Contact::create([
                'phone' => $phoneDigits,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'notes' => $request->input('notes'),
                'is_manually_saved' => true,
                'user_id' => Auth::id()
            ]);

            return response()->json(['success' => true, 'message' => 'Contact added successfully.']);
        } catch (\Exception $e) {
            Log::error('Error adding contact: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add contact.'], 500);
        }
    }

    /**
     * Update the specified contact.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|string',
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $contact = Contact::findOrFail($id);
        
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
                if (!in_array($contact->user_id, $userIds)) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized to update this contact.'], 403);
                }
            } else {
                if ($contact->user_id !== $user->id) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized to update this contact.'], 403);
                }
            }
        }

        $phone = preg_replace('/[^0-9+]/', '', $request->input('phone'));
        $phoneDigits = ltrim($phone, '+');

        // Check for uniqueness excluding self
        $exists = Contact::where('phone', $phoneDigits)->where('id', '!=', $id)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'A contact with this phone number already exists.'], 400);
        }

        try {
            $contact->update([
                'phone' => $phoneDigits,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'notes' => $request->input('notes'),
                'is_manually_saved' => true,
            ]);

            return response()->json(['success' => true, 'message' => 'Contact updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating contact: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update contact.'], 500);
        }
    }

    /**
     * Remove the specified contact.
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            
            $user = Auth::user();
            if (!$user->isSuperAdmin()) {
                if ($user->isAdmin()) {
                    $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                    $userIds = array_merge([$user->id], $clientIds);
                    if (!in_array($contact->user_id, $userIds)) {
                        return response()->json(['success' => false, 'message' => 'Unauthorized to delete this contact.'], 403);
                    }
                } else {
                    if ($contact->user_id !== $user->id) {
                        return response()->json(['success' => false, 'message' => 'Unauthorized to delete this contact.'], 403);
                    }
                }
            }
            
            $contact->delete();
            return response()->json(['success' => true, 'message' => 'Contact deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting contact: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete contact.'], 500);
        }
    }

    /**
     * Export contacts as CSV.
     */
    public function export()
    {
        $user = Auth::user();
        $query = Contact::query();

        if (!$user->isSuperAdmin()) {
            if ($user->isAdmin()) {
                $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                $userIds = array_merge([$user->id], $clientIds);
                $query->whereIn('user_id', $userIds);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        $contacts = $query->orderBy('created_at', 'desc')->get();
        
        $filename = "contacts_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Add CSV headers
        fputcsv($handle, ['Name', 'Phone', 'Email', 'Notes', 'Status', 'Added On']);
        
        foreach ($contacts as $contact) {
            $status = $contact->is_manually_saved ? 'Saved' : 'Auto';
            fputcsv($handle, [
                $contact->name,
                $contact->phone,
                $contact->email,
                $contact->notes,
                $status,
                $contact->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Manually save an auto contact.
     */
    public function saveAutoContact($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            
            $user = Auth::user();
            if (!$user->isSuperAdmin()) {
                if ($user->isAdmin()) {
                    $clientIds = \App\Models\User::where('created_by', $user->id)->pluck('id')->toArray();
                    $userIds = array_merge([$user->id], $clientIds);
                    if (!in_array($contact->user_id, $userIds)) {
                        return response()->json(['success' => false, 'message' => 'Unauthorized to modify this contact.'], 403);
                    }
                } else {
                    if ($contact->user_id !== $user->id) {
                        return response()->json(['success' => false, 'message' => 'Unauthorized to modify this contact.'], 403);
                    }
                }
            }
            
            $contact->update(['is_manually_saved' => true]);
            return response()->json(['success' => true, 'message' => 'Contact saved successfully.']);
        } catch (\Exception $e) {
            Log::error('Error saving auto contact: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save contact.'], 500);
        }
    }

    /**
     * Import contacts from a CSV file.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:csv,txt|max:10240', // max 10MB
            ]);

            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');
            
            $header = fgetcsv($handle);
            if (!$header) {
                return response()->json(['success' => false, 'message' => 'Invalid CSV format or empty file.'], 400);
            }

            // Standardize header names
            $header = array_map('strtolower', $header);
            $header = array_map('trim', $header);

            // Determine column indexes
            $nameIdx = array_search('name', $header);
            $phoneIdx = array_search('phone', $header);
            $emailIdx = array_search('email', $header);
            $notesIdx = array_search('notes', $header);

            if ($phoneIdx === false) {
                // If there's no header but it's a simple CSV, assume standard format: Name, Phone, Email, Notes
                // Let's reset pointer and assume index 0 = Name, 1 = Phone
                fclose($handle);
                $handle = fopen($file->getRealPath(), 'r');
                $nameIdx = 0;
                $phoneIdx = 1;
                $emailIdx = 2;
                $notesIdx = 3;
            }

            $importedCount = 0;
            $skippedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                // Safely extract data
                $name = isset($row[$nameIdx]) ? trim($row[$nameIdx]) : '';
                $phone = isset($row[$phoneIdx]) ? trim($row[$phoneIdx]) : '';
                $email = isset($row[$emailIdx]) ? trim($row[$emailIdx]) : '';
                $notes = isset($row[$notesIdx]) ? trim($row[$notesIdx]) : '';

                // Clean phone number (remove everything except digits and +)
                $phone = preg_replace('/[^0-9]/', '', $phone);
                
                // Minimum phone length check (basic validation)
                if (strlen($phone) < 7) {
                    $skippedCount++;
                    continue;
                }

                Contact::updateOrCreate(
                    ['phone' => $phone],
                    [
                        'name' => $name,
                        'email' => $email,
                        'notes' => $notes,
                        'is_manually_saved' => true, // CSV imports are manually saved
                        'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1 // Fallback to 1 if no auth (though usually auth middleware is present)
                    ]
                );
                
                $importedCount++;
            }

            fclose($handle);

            return response()->json([
                'success' => true, 
                'message' => "Successfully imported $importedCount contacts. Skipped $skippedCount invalid rows."
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error importing contacts: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to import contacts: ' . $e->getMessage()], 500);
        }
    }
}
