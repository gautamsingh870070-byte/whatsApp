@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold" style="color: var(--text-dark);">RCS Templates</h2>
        <button type="button" class="btn-primary" onclick="document.getElementById('addTemplateModal').style.display='flex'">
            <i class="fa-solid fa-plus"></i> Create Template
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="grid">
        @forelse($templates as $template)
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <h4 style="font-weight: 700; margin: 0; font-size: 16px;">{{ $template->name }}</h4>
                        <span style="background-color: #f1f5f9; font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: 600;">{{ strtoupper($template->type) }}</span>
                    </div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">Agent: {{ $template->agent->name ?? 'Unknown' }}</div>
                    
                    <div style="background: #f8fafc; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; border: 1px solid var(--border-color);">
                        @if($template->type === 'text')
                            {{ $template->content }}
                        @else
                            <i>Rich Card JSON Content</i>
                        @endif
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 12px;">
                    <span style="font-size: 12px; color: #166534; font-weight: 600;"><i class="fa-solid fa-check"></i> Approved</span>
                    <form action="{{ route('rcs.templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Delete this template?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background:none; border:none; color: #ef4444; cursor: pointer; font-size: 14px;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <i class="fa-solid fa-file-lines" style="font-size: 40px; color: var(--text-light); margin-bottom: 16px;"></i>
                <h4 style="font-weight: 600; color: var(--text-main);">No templates created yet</h4>
                <p style="color: var(--text-muted); font-size: 14px;">Create your first RCS template to start sending rich messages.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Template Modal -->
<div id="addTemplateModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:24px; width:100%; max-width:500px; max-height: 90vh; overflow-y: auto;">
        <h4 style="margin-top:0; font-weight:700;">Create RCS Template</h4>
        <form action="{{ route('rcs.templates.store') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-top: 16px;">
                <label class="form-label">Template Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">RCS Agent</label>
                <select name="rcs_agent_id" class="form-control" required>
                    <option value="">-- Select Agent --</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Template Type</label>
                <select name="type" class="form-control" required>
                    <option value="text">Text Only</option>
                    <option value="rich_card">Rich Card (JSON)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Content / JSON</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
                <small style="color: var(--text-muted); font-size: 11px;">If Rich Card, provide the full valid Google RBM JSON content payload.</small>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <button type="button" class="btn" style="padding: 10px 16px; background:#f1f5f9; border:none; border-radius:6px; cursor:pointer;" onclick="document.getElementById('addTemplateModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 10px 16px;"><i class="fa-solid fa-save"></i> Save Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
