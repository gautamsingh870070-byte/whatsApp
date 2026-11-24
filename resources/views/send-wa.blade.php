@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Outfit', sans-serif;
        background: #f4f7fa;
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 24px;
        border-radius: 12px;
        color: white;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.1);
    }
    
    .page-title .icon-bg {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .page-title h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
        letter-spacing: -0.5px;
    }
    .page-title p {
        margin: 4px 0 0 0;
        font-size: 14px;
        color: #cbd5e1;
    }

    .send-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
        background: #ffffff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 12px 35px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
    }

    /* Left Column */
    .numbers-col h4 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: #1e293b;
        letter-spacing: -0.3px;
    }

    /* Right Column */
    .input-group {
        display: flex;
        margin-bottom: 20px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        border: 1px solid #cbd5e1;
    }
    .input-group:focus-within {
        border-color: #6366f1;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.15);
    }
    
    .input-group-addon {
        background: linear-gradient(to right, #f8fafc, #f1f5f9);
        color: #475569;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        min-width: 140px;
        display: flex;
        align-items: center;
        border-right: 1px solid #cbd5e1;
    }
    
    .input-group-input {
        flex: 1;
        border: none;
        padding: 12px 20px;
        font-size: 14px;
        color: #1e293b;
        background: #fff;
        font-family: inherit;
    }
    .input-group-input:focus {
        outline: none;
    }

    .message-textarea {
        width: 100%;
        height: 160px;
        resize: none;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 16px;
        font-size: 14px;
        margin-bottom: 20px;
        font-family: inherit;
        color: #1e293b;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01) inset;
    }
    .message-textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    }

    /* Media & Buttons Row */
    .media-buttons-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .upload-box {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        font-size: 14px;
        color: #64748b;
        cursor: pointer;
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        transition: all 0.3s ease;
    }
    .upload-box:hover {
        background: #f1f5f9;
        border-color: #6366f1;
        color: #6366f1;
    }
    .upload-box i {
        font-size: 28px;
        margin-bottom: 12px;
        color: #94a3b8;
        transition: color 0.3s;
    }
    .upload-box:hover i {
        color: #6366f1;
    }
    .upload-box input[type="file"] {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%; height: 100%;
    }
    .upload-box a {
        text-decoration: none;
        color: #6366f1;
        font-weight: 600;
    }

    /* Button Inputs */
    .buttons-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .btn-input-group {
        display: flex;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        transition: all 0.3s;
    }
    .btn-input-group:focus-within {
        border-color: #6366f1;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    }
    .btn-input-addon {
        background: #f8fafc;
        color: #475569;
        padding: 12px 16px;
        font-weight: 600;
        font-size: 13px;
        min-width: 120px;
        border-right: 1px solid #cbd5e1;
        display: flex;
        align-items: center;
    }
    .btn-input-field {
        flex: 1;
        border: none;
        padding: 12px 16px;
        font-size: 14px;
        background: #fff;
    }
    .btn-input-field:focus {
        outline: none;
    }

    /* Warnings */
    .warning-text {
        color: #ef4444;
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
    }
    .warning-text i {
        font-size: 16px;
    }

    /* Footer */
    .form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 20px;
        margin-top: 30px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }
    .btn-submit {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        padding: 14px 40px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }
    .schedule-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        background: #f8fafc;
        padding: 10px 16px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
    }

    @media (max-width: 900px) {
        .send-layout {
            grid-template-columns: 1fr;
        }
        .media-buttons-row {
            grid-template-columns: 1fr;
        }
    }

    /* Tag Input Styles */
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #f8fafc;
        min-height: 180px;
        align-items: flex-start;
        cursor: text;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01) inset;
    }
    .tags-container:focus-within {
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    }
    .tag-item {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #1e3a8a;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #bfdbfe;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        animation: scaleIn 0.2s ease;
    }
    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .tag-item .remove-tag {
        cursor: pointer;
        color: #60a5fa;
        font-size: 12px;
        transition: color 0.2s;
    }
    .tag-item .remove-tag:hover {
        color: #ef4444;
    }
    .tag-input-field {
        border: none;
        outline: none;
        flex: 1;
        min-width: 250px;
        font-size: 14px;
        padding: 6px;
        background: transparent;
        font-family: inherit;
        color: #1e293b;
    }
    .tag-input-field::placeholder {
        color: #94a3b8;
    }

    .action-badge {
        background: rgba(255,255,255,0.15);
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
    }
</style>

<div class="page-title">
    <div class="icon-bg">
        <i class="fa-regular fa-envelope"></i>
    </div>
    <div>
        <h3>Wapp Btn Message</h3>
        <p>send btn wa message</p>
    </div>
    
    @isset($remainingQuota)
    <div style="margin-left: auto; font-weight: 600; color: #4a6ee0;">
        Daily Quota Remaining: {{ $remainingQuota }}
    </div>
    @endisset
</div>

<div class="warning-text">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span><strong>Note:</strong> Minimum 1000 numbers required for campaign. Do not send any fraud or abuse messages. If we find any such content, your account will be suspended.</span>
</div>

<!-- Alerts handled by SweetAlert below -->
<form action="{{ route('send.wa.submit') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="send-layout">
        
        <!-- Left Column: Numbers -->
        <div class="numbers-col">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h4>WhatsApp Numbers</h4>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ asset('public/demo_contacts.csv') }}" download style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #cbd5e1; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"><i class="fa-solid fa-download"></i> Demo</a>
                    <label style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; padding: 6px 12px; border-radius: 8px; font-size: 12px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 10px rgba(16,185,129,0.3); display: inline-flex; align-items: center; gap: 6px; margin: 0; transition: all 0.2s;">
                        <i class="fa-solid fa-file-excel"></i> Upload Excel
                        <input type="file" id="excelUploadInput" accept=".xlsx, .xls, .csv" style="display: none;">
                    </label>
                </div>
            </div>
            <div class="tags-container" id="phoneTagsContainer">
                <div id="tagsList" style="display: contents;"></div>
                <input type="text" id="phoneTagInput" class="tag-input-field" placeholder="Enter number & press Space/Enter" autocomplete="off">
            </div>
            <textarea name="phone_numbers" id="phone_numbers_hidden" style="display: none;" required>{{ old('phone_numbers') }}</textarea>
        </div>

        <!-- Right Column: Form -->
        <div class="form-col">
            <div class="input-group">
                <div class="input-group-addon">Campaign</div>
                <select name="campaign_name" id="campaign_select" class="input-group-input" required>
                    <option value="custom_campaign" {{ old('campaign_name') == 'custom_campaign' ? 'selected' : '' }}>Custom Message (Free-form / Media / Buttons)</option>
                    @if(!empty($metaTemplates))
                        @foreach($metaTemplates as $tpl)
                            <option value="{{ $tpl['name'] }}" {{ old('campaign_name') == $tpl['name'] ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $tpl['name'])) }} (Meta Template - {{ $tpl['status'] }})
                            </option>
                        @endforeach
                    @else
                        <option value="stm_digital" {{ old('campaign_name') == 'stm_digital' ? 'selected' : '' }}>STM Digital</option>
                        <option value="stm_customer_support" {{ old('campaign_name') == 'stm_customer_support' ? 'selected' : '' }}>STM Developments Official</option>
                        <option value="3p_direct_integration_test_template" {{ old('campaign_name') == '3p_direct_integration_test_template' ? 'selected' : '' }}>3P Direct Integration Test</option>
                    @endif
                </select>
            </div>

            <div class="input-group">
                <div class="input-group-addon"><i class="fa-regular fa-clock"></i> Schedule (Optional)</div>
                <input type="datetime-local" name="scheduled_at" class="input-group-input" value="{{ old('scheduled_at') }}">
            </div>

            <textarea name="message_text" id="message_text" class="message-textarea" placeholder="If using Template: Enter dynamic variables separated by commas (e.g. Pooja Offer, 500)&#10;If Custom Message: Type full message text here" maxlength="300">{{ old('message_text') }}</textarea>
            
            <div id="dynamic_variables_container" style="display: none; margin-bottom: 16px;">
                <!-- dynamic inputs will go here -->
            </div>

            <div class="media-buttons-row">
                <!-- Image/Media Upload -->
                <!-- <div class="upload-box-wrapper">
                    <div class="upload-box" id="drop-area">
                        Drag & Drop Media(image, pdf) or <br> <a>Click to Browse Media</a>
                        <input type="file" name="media_file" accept=".jpg,.jpeg,.png,.pdf,.mp4">
                    </div>
                    <div class="upload-subtitle text-green">Image Upload <span style="color:red">(Max file size 2 MB.)</span></div>
                </div> -->

                <div class="upload-box-wrapper">
    <div class="upload-box" id="drop-area">
        Drag & Drop Media (Images, PDF, Documents) or
        <br>
        <a href="javascript:void(0)">Click to Browse Media</a>

        <input 
            type="file"
            name="media_files[]"
            id="mediaFilesInput"
            accept=".jpg,.jpeg,.png,.gif,.pdf,.mp4,.doc,.docx,.xlsx,.xls,.cdr,.pmd,.psd"
            multiple
        >
    </div>

    <div class="upload-subtitle text-green">
        Multiple Files Upload
        <span style="color:red">(Max file size 2 MB each.)</span>
    </div>

    <div id="selectedFilesPreview"></div>
</div>

                <!-- Call to Action Buttons -->
                <div class="buttons-wrapper">
                    <div class="btn-input-group">
                        <div class="btn-input-addon">Visit Now</div>
                        <input type="url" name="visit_url" class="btn-input-field" placeholder="http://" value="{{ old('visit_url') }}">
                    </div>
                    <div class="btn-input-group">
                        <div class="btn-input-addon">Call Now</div>
                        <input type="text" name="call_number" class="btn-input-field" placeholder="10 Digit number" value="{{ old('call_number') }}">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <div class="form-footer">
        <button type="submit" class="btn-submit" {{ isset($remainingQuota) && $remainingQuota <= 0 ? 'disabled' : '' }}>
            Submit Now <i class="fa-solid fa-paper-plane" style="margin-left: 4px;"></i>
        </button>
        <label class="schedule-wrap">
            <input type="checkbox" name="is_scheduled"> Schedule
        </label>
    </div>
</form>

<script>
    const mediaInput = document.getElementById('mediaFilesInput');
    if (mediaInput) {
        mediaInput.addEventListener('change', function(e) {
            let files = e.target.files;
            let box = document.getElementById('selectedFilesPreview');
            if(files && files.length > 0) {
                let fileNames = [];
                for (let i = 0; i < files.length; i++) {
                    fileNames.push(files[i].name);
                }
                box.innerHTML = '<div style="color:#009688; font-weight:600; margin-top: 10px;">Selected: <br>' + fileNames.join('<br>') + '</div>';
            } else {
                box.innerHTML = '';
            }
        });
    }
</script> 

<!-- Recent Activity Table -->
<div style="margin-top: 40px;">
    <h3 style="font-size: 16px; margin-bottom: 16px; color: #333;">Recent Activity</h3>
    <div style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                    <th style="padding: 12px 16px; font-weight: 600; color: #555;">Date</th>
                    <th style="padding: 12px 16px; font-weight: 600; color: #555;">Campaign</th>
                    <th style="padding: 12px 16px; font-weight: 600; color: #555;">Phone</th>
                    <th style="padding: 12px 16px; font-weight: 600; color: #555;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 16px; color: #666;">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    <td style="padding: 12px 16px; font-weight: 500;">{{ $log->template_name }}</td>
                    <td style="padding: 12px 16px; color: #666;">{{ $log->phone_number }}</td>
                    <td style="padding: 12px 16px;">
                        @php
                            $statusLabel = ucfirst($log->status);
                            $bg = '#ffebee'; $color = '#c62828'; // Default Failed
                            
                            if (in_array($log->status, ['success', 'sent'])) {
                                $bg = '#e0f2fe'; $color = '#0284c7'; // Blue for sent
                            } elseif ($log->status === 'delivered') {
                                $bg = '#fef08a'; $color = '#854d0e'; // Yellow for delivered
                            } elseif ($log->status === 'read') {
                                $bg = '#dcfce7'; $color = '#166534'; // Green for read
                            } elseif ($log->status === 'pending') {
                                $bg = '#f3f4f6'; $color = '#4b5563'; // Gray for pending
                            }
                        @endphp
                        <span style="background: {{ $bg }}; color: {{ $color }}; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding: 24px; text-align: center; color: #999;">No recent messages sent.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- SweetAlert2 integration -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- SheetJS for Excel Parsing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    const metaTemplates = @json($metaTemplates ?? []);
    const campaignSelect = document.getElementById('campaign_select');
    const messageText = document.getElementById('message_text');
    const dynamicVarsContainer = document.getElementById('dynamic_variables_container');

    function updateTemplateUI() {
        if (!campaignSelect) return;
        const selected = campaignSelect.value;
        
        if (selected === 'custom_campaign' || selected === 'stm_digital' || selected === 'stm_customer_support' || selected === '3p_direct_integration_test_template') {
            messageText.style.display = 'block';
            messageText.readOnly = false;
            dynamicVarsContainer.style.display = 'none';
            dynamicVarsContainer.innerHTML = '';
            return;
        }

        const tpl = metaTemplates.find(t => t.name === selected);
        if (tpl) {
            let bodyText = '';
            if (tpl.components) {
                const bodyComp = tpl.components.find(c => c.type === 'BODY');
                if (bodyComp) {
                    bodyText = bodyComp.text || '';
                }
            }

            const regex = /\{\{(\d+)\}\}/g;
            let match;
            let maxVar = 0;
            while ((match = regex.exec(bodyText)) !== null) {
                let num = parseInt(match[1]);
                if (num > maxVar) maxVar = num;
            }

            if (maxVar > 0) {
                messageText.style.display = 'none'; 
                dynamicVarsContainer.style.display = 'block';
                dynamicVarsContainer.innerHTML = '<label style="display:block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Template Variables Required:</label>';
                
                for (let i = 1; i <= maxVar; i++) {
                    dynamicVarsContainer.innerHTML += `
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="background: #fde68a; color: #92400e; padding: 8px 12px; border-radius: 4px 0 0 4px; font-weight: 600;">&#123;&#123;${i}&#125;&#125;</span>
                            <input type="text" class="template-var-input" data-index="${i}" placeholder="Enter value for &#123;&#123;${i}&#125;&#125;" required style="flex: 1; border: 1px solid #cbd5e1; border-left: none; border-radius: 0 4px 4px 0; padding: 8px 12px; font-size: 14px;">
                        </div>
                    `;
                }

                const inputs = dynamicVarsContainer.querySelectorAll('.template-var-input');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        let vals = [];
                        inputs.forEach(inp => vals.push(inp.value.replace(/,/g, '')));
                        messageText.value = vals.join(',');
                    });
                });
                
                let vals = [];
                inputs.forEach(inp => vals.push(inp.value.replace(/,/g, '')));
                messageText.value = vals.join(',');

            } else {
                messageText.style.display = 'none';
                messageText.value = '';
                dynamicVarsContainer.style.display = 'block';
                dynamicVarsContainer.innerHTML = '<div style="color: #10b981; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-check"></i> This template requires no variables. You can just send it!</div>';
            }
        }
    }

    if (campaignSelect) {
        campaignSelect.addEventListener('change', updateTemplateUI);
        setTimeout(updateTemplateUI, 100);
    }


    // Excel Upload Logic
    document.getElementById('excelUploadInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        Swal.fire({
            title: 'Processing File',
            text: 'Extracting phone numbers...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const json = XLSX.utils.sheet_to_json(firstSheet, {header: 1});
                
                let phoneNumbers = [];
                
                json.forEach(row => {
                    row.forEach(cell => {
                        if (cell) {
                            let str = String(cell).replace(/\D/g, ''); // strip non-digits
                            if (str.length >= 10 && str.length <= 15) {
                                phoneNumbers.push(str);
                            }
                        }
                    });
                });

                if (phoneNumbers.length > 0) {
                    const textarea = document.querySelector('textarea[name="phone_numbers"]');
                    let existing = textarea.value.trim();
                    let combined = existing ? existing + '\n' + phoneNumbers.join('\n') : phoneNumbers.join('\n');
                    
                    // Deduplicate
                    let uniqueNumbers = [...new Set(combined.split('\n').map(n => n.trim()).filter(n => n))];
                    textarea.value = uniqueNumbers.join('\n');
                    
                    // Update tags UI
                    if(typeof window.refreshTags === 'function') { window.refreshTags(); }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Excel Processed',
                        text: `Found ${phoneNumbers.length} numbers (Total unique in box: ${uniqueNumbers.length}).`,
                        confirmButtonColor: '#4a6ee0'
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Numbers Found',
                        text: 'Could not find any valid phone numbers (10-15 digits) in the file.',
                        confirmButtonColor: '#f05a5b'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error reading file',
                    text: err.message,
                    confirmButtonColor: '#f05a5b'
                });
            }
            document.getElementById('excelUploadInput').value = '';
        };
        reader.readAsArrayBuffer(file);
    });

    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: {!! json_encode(session('success')) !!},
            icon: 'success',
            confirmButtonColor: '#4a6ee0',
            confirmButtonText: 'Great'
        });
    @endif

    @if(session('status'))
        Swal.fire({
            title: 'Campaign Status',
            html: {!! json_encode(session('status')) !!},
            icon: 'info',
            confirmButtonColor: '#4a6ee0',
            confirmButtonText: 'OK'
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
            confirmButtonColor: '#f05a5b',
            confirmButtonText: 'Try Again'
        });
    @endif

    // --- Phone Number Tags Logic ---
    const tagInput = document.getElementById('phoneTagInput');
    const tagsContainer = document.getElementById('phoneTagsContainer');
    const tagsList = document.getElementById('tagsList');
    const hiddenTextarea = document.getElementById('phone_numbers_hidden');

    function getTags() {
        return hiddenTextarea.value.split(/[\s,]+/).filter(t => t.trim() !== '');
    }

    function setTags(newTags) {
        hiddenTextarea.value = newTags.join('\n');
    }

    window.refreshTags = function() {
        const tags = getTags();
        tagsList.innerHTML = '';
        tags.forEach((tag, index) => {
            const tagEl = document.createElement('div');
            tagEl.className = 'tag-item';
            tagEl.innerHTML = `${tag} <i class="fa-solid fa-xmark remove-tag" onclick="removeTag(${index})"></i>`;
            tagsList.appendChild(tagEl);
        });
    };

    window.removeTag = function(index) {
        const tags = getTags();
        tags.splice(index, 1);
        setTags(tags);
        refreshTags();
    };

    function processTagInput(value) {
        const currentTags = getTags();
        const newTags = value.split(/[\s,]+/).map(t => t.replace(/[^0-9]/g, '')).filter(t => t.length >= 10);
        
        let updated = false;
        newTags.forEach(t => {
            if (!currentTags.includes(t)) {
                currentTags.push(t);
                updated = true;
            }
        });
        
        if(updated) {
            setTags(currentTags);
            refreshTags();
        }
    }

    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === ',') {
            e.preventDefault();
            if(this.value.trim() !== '') {
                processTagInput(this.value);
                this.value = '';
            }
        } else if (e.key === 'Backspace' && this.value === '') {
            const tags = getTags();
            if (tags.length > 0) {
                tags.pop();
                setTags(tags);
                refreshTags();
            }
        }
    });

    tagInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text');
        processTagInput(pasted);
        this.value = ''; // Clear input after paste
    });

    tagInput.addEventListener('input', function(e) {
        let val = this.value;
        if (/[\s,]/.test(val)) {
            processTagInput(val);
            this.value = '';
            return;
        }
        let digits = val.replace(/[^0-9]/g, '');
        if (digits.length === 10 && val.length === 10) {
            processTagInput(digits);
            this.value = '';
        }
    });

    tagsContainer.addEventListener('click', () => tagInput.focus());

    // Init tags on load
    refreshTags();

</script>
@endsection
