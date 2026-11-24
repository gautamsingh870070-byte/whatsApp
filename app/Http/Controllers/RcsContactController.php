<?php

namespace App\Http\Controllers;

use App\Models\RcsContact;
use Illuminate\Http\Request;

class RcsContactController extends Controller
{
    public function index()
    {
        $contacts = RcsContact::latest()->paginate(20);
        return view('rcs.contacts', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'country_code' => 'required|string',
            'name' => 'nullable|string'
        ]);

        RcsContact::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
        ]);

        return back()->with('success', 'Contact added successfully.');
    }

    public function destroy($id)
    {
        $contact = RcsContact::findOrFail($id);
        $contact->delete();
        return back()->with('success', 'Contact deleted successfully.');
    }
}
