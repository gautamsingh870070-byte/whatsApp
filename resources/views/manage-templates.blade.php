@extends('layouts.app')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .page-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .page-title .icon-bg {
        background-color: #e0f2fe;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #0284c7;
    }
    .page-title h3 {
        margin: 0;
        font-size: 18px;
        color: #0f172a;
    }
    .page-title p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }
    .btn-create-template {
        background-color: #25D366;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3);
    }
    .btn-create-template:hover {
        background-color: #1ebd59;
    }
    .template-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
    .template-card {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .template-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }
    .template-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
    }
    .badge-approved { background: #dcfce7; color: #166534; }
    .badge-pending { background: #fef9c3; color: #854d0e; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }
    .badge-utility { background: #e0f2fe; color: #075985; }
    .badge-marketing { background: #fae8ff; color: #86198f; }

    /* Modal Form */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-card {
        background: #ffffff;
        width: 100%;
        max-width: 580px;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        max-height: 90vh;
        overflow-y: auto;
    }
    
    /* CKEditor Custom Styles */
    .ck-editor__editable_inline {
        min-height: 120px;
    }
</style>

<div class="page-header">
    <div class="page-title">
        <div class="icon-bg">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
        <div>
            <h3>Meta WhatsApp Templates</h3>
            <p>View approved templates & submit new custom templates to Meta Account</p>
        </div>
    </div>
    @if(Auth::user()->hasPermission('templates.create'))
    <button class="btn-create-template" onclick="openModal()">
        <i class="fa-solid fa-plus"></i> Create New Template
    </button>
    @endif
</div>

<!-- Templates Grid -->
<div class="template-grid">
    @forelse($templates as $tpl)
    <div class="template-card">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span class="template-badge badge-{{ strtolower($tpl['category'] ?? 'utility') }}">
                    {{ $tpl['category'] ?? 'UTILITY' }}
                </span>
                <span class="template-badge badge-{{ strtolower($tpl['status'] ?? 'approved') }}">
                    {{ $tpl['status'] ?? 'APPROVED' }}
                </span>
            </div>

            <h4 style="margin: 0 0 6px 0; font-size: 15px; color: #0f172a;">{{ $tpl['name'] }}</h4>
            <div style="font-size: 12px; color: #64748b; margin-bottom: 12px;">Language: <strong>{{ $tpl['language'] }}</strong></div>

            <div style="background: #f8fafc; border-radius: 6px; padding: 12px; font-size: 13px; color: #334155; border: 1px solid #f1f5f9; line-height: 1.5;">
                @foreach($tpl['components'] ?? [] as $comp)
                    @if($comp['type'] === 'HEADER')
                        @if(isset($comp['text']))
                            <div style="font-weight: 700; margin-bottom: 6px; color: #0f172a;">{{ $comp['text'] }}</div>
                        @elseif(($comp['format'] ?? '') === 'IMAGE')
                            @if(!empty($tpl['local_image_url']))
                                <div style="margin-bottom: 10px; border-radius: 6px; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; max-height: 160px;">
                                    <img src="{{ $tpl['local_image_url'] }}" onerror="this.onerror=null; this.src='{{ $tpl['local_image_url_fallback'] }}';" alt="Template Image" style="width: 100%; height: auto; object-fit: cover;">
                                </div>
                            @else
                                <div style="font-weight: 600; margin-bottom: 6px; color: #4a6ee0;"><i class="fa-regular fa-image"></i> [Image Banner]</div>
                            @endif
                        @endif
                    @elseif($comp['type'] === 'BODY' && isset($comp['text']))
                        <div>{{ $comp['text'] }}</div>
                    @elseif($comp['type'] === 'FOOTER' && isset($comp['text']))
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 6px;">{{ $comp['text'] }}</div>
                    @endif
                @endforeach
            </div>

            @if(strtoupper($tpl['status'] ?? '') !== 'APPROVED')
                @if(strtoupper($tpl['status'] ?? '') === 'REJECTED')
                <div style="margin-top: 12px; font-size: 11.5px; color: #991b1b; background: #fee2e2; padding: 8px 12px; border-radius: 6px; border: 1px solid #fecaca; display: flex; align-items: flex-start; gap: 8px;">
                    <i class="fa-solid fa-circle-xmark" style="margin-top: 2px;"></i>
                    <div style="line-height: 1.4;">
                        This template was <strong>rejected</strong> by Meta. Please review their policy guidelines.
                    </div>
                </div>
                @else
                <div style="margin-top: 12px; font-size: 11.5px; color: #854d0e; background: #fef9c3; padding: 8px 12px; border-radius: 6px; border: 1px solid #fde047; display: flex; align-items: flex-start; gap: 8px;">
                    <i class="fa-regular fa-clock" style="margin-top: 2px;"></i>
                    <div style="line-height: 1.4;">
                        Approval usually takes <strong>2-24 hours</strong>. Meta is currently reviewing it.
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 40px; background: #fff; border-radius: 12px; border: 2px dashed #e2e8f0; color: #64748b;">
        <div style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;">
            <i class="fa-regular fa-folder-open"></i>
        </div>
        <h4 style="color: #0f172a; font-size: 18px; margin-bottom: 8px; font-weight: 700;">No Templates Found</h4>
        <p style="margin-bottom: 24px; font-size: 14px; max-width: 400px; margin-left: auto; margin-right: auto;">It looks like you haven't created any templates yet. Get started by creating your first WhatsApp template to send to your customers.</p>
        @if(Auth::user()->hasPermission('templates.create'))
        <button class="btn-create-template" onclick="openModal()" style="margin: 0 auto; padding: 12px 24px; font-size: 15px;">
            <i class="fa-solid fa-plus"></i> Create New Template
        </button>
        @endif
    </div>
    @endforelse
</div>

<!-- Modal Form -->
<div class="modal-overlay" id="templateModal">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; color: #0f172a;">Create Meta Template</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="templateForm" action="{{ route('templates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Template Name (Snake Case)</label>
                <input type="text" name="name" placeholder="e.g. stm_special_offer" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Category</label>
                    <select name="category" id="categorySelect" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        <option value="UTILITY">Utility</option>
                        <option value="MARKETING">Marketing</option>
                        <option value="AUTHENTICATION">Authentication</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Language</label>
                    <select name="language" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        <option value="en_US">English (US)</option>
                        <option value="en">English</option>
                        <option value="hi">Hindi</option>
                    </select>
                </div>
            </div>

            <!-- Marketing Sub-Types -->
            <div id="marketingTypeContainer" style="display: none; margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Type of message</label>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc;">
                        <input type="radio" name="marketing_type" value="DEFAULT" checked style="margin-top: 4px; cursor: pointer;">
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a; margin-bottom: 2px;">Custom (Default)</div>
                            <div style="font-size: 12px; color: #64748b; line-height: 1.4;">Send messages with media and customized buttons to engage your customers.</div>
                        </div>
                    </label>

                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <input type="radio" name="marketing_type" value="CATALOG" style="margin-top: 4px; cursor: pointer;">
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a; margin-bottom: 2px;">Catalog</div>
                            <div style="font-size: 12px; color: #64748b; line-height: 1.4;">Send messages that drive sales by connecting your product catalog.</div>
                        </div>
                    </label>
                    
                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <input type="radio" name="marketing_type" value="FLOWS" style="margin-top: 4px; cursor: pointer;">
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a; margin-bottom: 2px;">Flows</div>
                            <div style="font-size: 12px; color: #64748b; line-height: 1.4;">Send a form to capture customer interests, appointment requests or run surveys.</div>
                        </div>
                    </label>
                    
                    <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <input type="radio" name="marketing_type" value="CALLING" style="margin-top: 4px; cursor: pointer;">
                        <div>
                            <div style="font-weight: 600; font-size: 13px; color: #0f172a; margin-bottom: 2px;">Calling permissions request</div>
                            <div style="font-size: 12px; color: #64748b; line-height: 1.4;">Ask customers if you can call them on WhatsApp.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div style="margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background: #f8fafc;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 8px;">Header (Optional)</label>
                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                    <label style="font-size: 13px;"><input type="radio" name="header_type" value="NONE" checked onchange="toggleHeaderType()"> None</label>
                    <label style="font-size: 13px;"><input type="radio" name="header_type" value="TEXT" onchange="toggleHeaderType()"> Text</label>
                    <label style="font-size: 13px;"><input type="radio" name="header_type" value="IMAGE" onchange="toggleHeaderType()"> Image (Banner)</label>
                </div>
                
                <div id="headerTextWrapper" style="display: none;">
                    <input type="text" name="header_text" placeholder="e.g. STM Digital" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                </div>
                <div id="headerImageWrapper" style="display: none;">
                    <input type="file" name="header_image" accept="image/jpeg,image/png,image/jpg" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; background: white;">
                    <small style="display: block; color: #64748b; margin-top: 4px;">Upload an image (JPG/PNG). Used as a banner for your message.</small>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Body Message Text</label>
                <input type="hidden" name="body_text" id="hidden_body_text">
                <textarea name="body_text_html" id="body_text_editor" placeholder="Type template message body..." style="width: 100%; height: 120px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; resize: none;"></textarea>
                <p style="font-size: 11px; color: #64748b; margin-top: 6px;">Note: Formatting (Bold, Italic, Strikethrough) will be converted to WhatsApp markup (*bold*, _italic_, ~strikethrough~) when sent to Meta.</p>
                
                <div id="variableSamplesContainer" style="margin-top: 12px; display: none; background: #fffbeb; border: 1px solid #fde68a; padding: 12px; border-radius: 8px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #92400e; margin-bottom: 8px;"><i class="fa-solid fa-lightbulb"></i> Sample Values for Variables (Required by Meta)</label>
                    <p style="font-size: 11px; color: #b45309; margin-top: 0; margin-bottom: 10px;">Please provide a realistic example for each variable so Meta can understand the context and approve it faster.</p>
                    <div id="variableSamplesList" style="display: flex; flex-direction: column; gap: 8px;"></div>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Footer Text (Optional)</label>
                <input type="text" name="footer_text" placeholder="e.g. - STM Developments" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background: #f8fafc;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 8px;">Buttons (Optional)</label>
                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                    <label style="font-size: 13px;"><input type="radio" name="button_type" value="NONE" checked onchange="toggleButtonType()"> None</label>
                    <label style="font-size: 13px;"><input type="radio" name="button_type" value="URL" onchange="toggleButtonType()"> Call to Action (Website)</label>
                </div>
                
                <div id="buttonUrlWrapper" style="display: none; grid-template-columns: 1fr 2fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 12px; color: #475569; margin-bottom: 4px;">Button Text</label>
                        <input type="text" name="button_text" placeholder="e.g. Book Now" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; color: #475569; margin-bottom: 4px;">Website URL</label>
                        <input type="url" name="button_url" placeholder="https://stmdevel.in/booking" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" id="submitTemplateBtn" style="background: #25D366; color: #ffffff; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer;">Submit to Meta</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openModal() {
        document.getElementById('templateModal').classList.add('active');
    }
    function closeModal() {
        document.getElementById('templateModal').classList.remove('active');
    }

    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: {!! json_encode(session('success')) !!},
            icon: 'success',
            confirmButtonColor: '#25D366',
            confirmButtonText: 'Great'
        });
    @endif

    @if($errors->any())
        let errorMsg = "";
        @foreach($errors->all() as $error)
            errorMsg += {!! json_encode($error) !!} + "<br>";
        @endforeach
        
        Swal.fire({
            title: 'Action Failed',
            html: errorMsg,
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Try Again'
        });
    @endif
    
    // Toggle Marketing Sub-Types based on Category
    document.getElementById('categorySelect').addEventListener('change', function() {
        const container = document.getElementById('marketingTypeContainer');
        if (this.value === 'MARKETING') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            document.querySelector('input[name="marketing_type"][value="DEFAULT"]').checked = true;
        }
    });

    function toggleHeaderType() {
        const type = document.querySelector('input[name="header_type"]:checked').value;
        document.getElementById('headerTextWrapper').style.display = type === 'TEXT' ? 'block' : 'none';
        document.getElementById('headerImageWrapper').style.display = type === 'IMAGE' ? 'block' : 'none';
    }

    function toggleButtonType() {
        const type = document.querySelector('input[name="button_type"]:checked').value;
        document.getElementById('buttonUrlWrapper').style.display = type === 'URL' ? 'grid' : 'none';
    }
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    let ckEditorInstance;

    ClassicEditor
        .create(document.querySelector('#body_text_editor'), {
            toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'undo', 'redo']
        })
        .then(editor => {
            ckEditorInstance = editor;
            editor.model.document.on('change:data', () => {
                updateVariableSamples(editor.getData());
            });
        })
        .catch(error => {
            console.error(error);
        });

    function updateVariableSamples(html) {
        const text = convertHtmlToWhatsApp(html);
        const regex = /\{\{(\d+)\}\}/g;
        let matches;
        let maxVar = 0;
        
        while ((matches = regex.exec(text)) !== null) {
            let num = parseInt(matches[1]);
            if (num > maxVar) maxVar = num;
        }
        
        const container = document.getElementById('variableSamplesContainer');
        const list = document.getElementById('variableSamplesList');
        
        if (maxVar > 0) {
            container.style.display = 'block';
            let existingInputs = {};
            list.querySelectorAll('input').forEach(input => {
                existingInputs[input.name] = input.value;
            });
            
            list.innerHTML = '';
            for (let i = 1; i <= maxVar; i++) {
                let name = `example_values[${i}]`;
                let val = existingInputs[name] || '';
                list.innerHTML += `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 13px; font-weight: 600; background: #fde68a; color: #92400e; padding: 4px 8px; border-radius: 4px;">&#123;&#123;${i}&#125;&#125;</span>
                        <input type="text" name="${name}" placeholder="e.g. Ramesh" value="${val}" required style="flex: 1; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    </div>
                `;
            }
        } else {
            container.style.display = 'none';
            list.innerHTML = '';
        }
    }

    function convertHtmlToWhatsApp(html) {
        let text = html;
        
        // Convert basic formatting
        text = text.replace(/<(strong|b)[^>]*>(.*?)<\/\1>/gi, '*$2*');
        text = text.replace(/<(em|i)[^>]*>(.*?)<\/\1>/gi, '_$2_');
        text = text.replace(/<(s|strike|del)[^>]*>(.*?)<\/\1>/gi, '~$2~');
        text = text.replace(/<code[^>]*>(.*?)<\/code>/gi, '```$1```');
        
        // Handle list items
        text = text.replace(/<li[^>]*>(.*?)<\/li>/gi, '- $1\n');
        
        // Handle paragraphs and breaks
        text = text.replace(/<\/p>/gi, '\n\n');
        text = text.replace(/<br\s*\/?>/gi, '\n');
        
        // Strip all remaining HTML tags
        text = text.replace(/<[^>]+>/g, '');
        
        // Decode HTML entities
        const doc = new DOMParser().parseFromString(text, "text/html");
        text = doc.documentElement.textContent;
        
        // Clean up excessive newlines
        text = text.replace(/\n\n\n+/g, '\n\n');
        
        return text.trim();
    }

    document.getElementById('templateForm').addEventListener('submit', function(e) {
        if (ckEditorInstance) {
            const htmlData = ckEditorInstance.getData();
            if (!htmlData.trim()) {
                e.preventDefault();
                Swal.fire('Required', 'Body message text is required.', 'warning');
                return;
            }
            // Convert to WhatsApp format and set it back to the hidden input
            document.querySelector('#hidden_body_text').value = convertHtmlToWhatsApp(htmlData);
        }
    });
</script>
@endsection
