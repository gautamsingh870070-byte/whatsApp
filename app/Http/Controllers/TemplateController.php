<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Display live Meta templates
     */
    public function index()
    {
        $templates = $this->whatsAppService->getMetaTemplates();
        
        if (\Illuminate\Support\Facades\Schema::hasTable('user_templates')) {
            $userTemplates = \App\Models\UserTemplate::whereIn('template_name', collect($templates)->pluck('name'))->get()->keyBy('template_name');
            foreach ($templates as &$tpl) {
                if (isset($userTemplates[$tpl['name']])) {
                    $compData = json_decode($userTemplates[$tpl['name']]->components, true);
                    if (!empty($compData['local_image_path'])) {
                        $tpl['local_image_url'] = asset('storage/' . $compData['local_image_path']);
                        $tpl['local_image_url_fallback'] = asset('public/storage/' . $compData['local_image_path']);
                    }
                }
            }
        }
        
        return view('manage-templates', compact('templates'));
    }

    /**
     * Fetch live Meta templates (API)
     */
    public function fetchTemplates()
    {
        try {
            $templates = $this->whatsAppService->getMetaTemplates();
            return response()->json(['success' => true, 'templates' => $templates]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store/Submit a new template to Meta WhatsApp Account
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:512',
            'category'    => 'required|string|in:UTILITY,MARKETING,AUTHENTICATION',
            'language'    => 'required|string',
            'body_text'   => 'required|string|max:1024',
            'header_type' => 'nullable|string|in:TEXT,IMAGE',
            'header_text' => 'nullable|string|max:60',
            'header_image'=> 'nullable|file|mimes:jpeg,png,jpg|max:5120',
            'footer_text' => 'nullable|string|max:60',
            'button_type' => 'nullable|string|in:NONE,URL',
            'button_text' => 'nullable|string|max:25',
            'button_url'  => 'nullable|url|max:2000',
            'example_values' => 'nullable|array',
            'example_values.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        // Handle Image Upload for Header
        if ($request->hasFile('header_image')) {
            // Save locally for automatic sending later
            $path = $request->file('header_image')->store('template_images', 'public');
            $data['local_image_path'] = $path;

            $handle = $this->whatsAppService->uploadTemplateMedia($request->file('header_image'), 'image/jpeg');
            if ($handle) {
                $data['header_handle'] = $handle;
                $data['header_type'] = 'IMAGE';
            } else {
                return redirect()->back()->withErrors(['template_error' => 'Failed to upload image to Meta for Template. Please make sure WHATSAPP_APP_ID is configured in your .env file.'])->withInput();
            }
        }

        $result = $this->whatsAppService->createMetaTemplate($data);

        if ($result['success']) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('user_templates')) {
                    \App\Models\UserTemplate::create([
                        'user_id' => auth()->id(),
                        'template_name' => $request->name,
                        'category' => $request->category,
                        'language' => $request->language,
                        'components' => json_encode($data),
                        'status' => 'PENDING',
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore DB error, template is on Meta
            }
            return redirect()->back()->with('success', 'Template submitted successfully to Meta for approval!');
        }

        $errorMsg = $result['response']['error']['message'] ?? 'Failed to create template on Meta.';
        return redirect()->back()->withErrors(['template_error' => $errorMsg])->withInput();
    }
}
