@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 1000px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold" style="color: var(--text-dark);">Send RCS Message</h2>
        <a href="{{ route('rcs.history') }}" class="btn-primary" style="background-color: var(--text-main);">
            <i class="fa-solid fa-clock-rotate-left"></i> View History
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 24px;">{{ session('error') }}</div>
    @endif

    <div class="form-card">
        <form action="{{ route('rcs.send.submit') }}" method="POST">
            @csrf
            
            <div class="grid" style="grid-template-columns: 1fr 1fr;">
                <div class="form-group">
                    <label class="form-label">RCS Agent</label>
                    <select name="agent_id" class="form-control" required>
                        <option value="">-- Select Agent --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->brand_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Customer Mobile Number</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="country_code" class="form-control" value="+91" style="width: 80px;" required>
                        <input type="text" name="phone_number" class="form-control" placeholder="10-digit Mobile Number" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Message Type</label>
                <select name="message_type" id="message_type" class="form-control" required onchange="toggleMessageType()">
                    <option value="text">Text Message</option>
                    <option value="template">Template</option>
                    <option value="rich_card">Rich Card</option>
                </select>
            </div>

            <div class="form-group" id="text_message_group">
                <label class="form-label">Message Text</label>
                <textarea name="text_message" class="form-control" rows="5" placeholder="Enter your message here..."></textarea>
            </div>

            <div class="form-group" id="template_group" style="display: none;">
                <label class="form-label">Select Template</label>
                <select name="template_id" class="form-control">
                    <option value="">-- Select Template --</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Preview section placeholder -->
            <div style="background-color: #f8fafc; padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin: 20px 0;">
                <h5 style="margin-bottom: 12px; font-weight: 600; font-size: 14px; color: var(--text-main);">RCS Preview</h5>
                <div style="background-color: white; border-radius: 12px; padding: 16px; max-width: 320px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); position: relative;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                        <div style="width: 24px; height: 24px; background-color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 10px;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span style="font-weight: 700; font-size: 13px;">Verified Sender</span>
                    </div>
                    <div id="preview_text" style="font-size: 14px; line-height: 1.5; color: #1e293b;">
                        Your message will appear here...
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top: 24px;">
                <button type="submit" class="btn-primary" style="padding: 12px 28px; font-size: 16px;">
                    <i class="fa-solid fa-paper-plane"></i> Send RCS Message
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleMessageType() {
        const type = document.getElementById('message_type').value;
        document.getElementById('text_message_group').style.display = type === 'text' ? 'block' : 'none';
        document.getElementById('template_group').style.display = type === 'template' ? 'block' : 'none';
    }

    document.querySelector('textarea[name="text_message"]').addEventListener('input', function(e) {
        document.getElementById('preview_text').innerText = e.target.value || 'Your message will appear here...';
    });
</script>
@endsection
