@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold" style="color: var(--text-dark);">RCS Agent Settings</h2>
        <button type="button" class="btn-primary" onclick="document.getElementById('addAgentModal').style.display='flex'">
            <i class="fa-solid fa-plus"></i> Configure Agent
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="margin-bottom: 24px;">
        <h4 style="font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-brands fa-google" style="color: #ea4335;"></i> Google RCS Configuration
        </h4>
        <p style="font-size: 14px; color: var(--text-muted); line-height: 1.5; margin-bottom: 0;">
            To send RCS messages, you must have an approved Google RCS Business Messaging Agent. <br>
            You need to provide the Service Account JSON credentials generated from the Google Cloud Console. <br><br>
            <strong>Webhook Setup:</strong> Once you add an agent, a Webhook URL will be generated. You must register this URL in the Google RBM Console to receive delivery reports and incoming messages.
        </p>
    </div>

    <div class="grid">
        @forelse($agents as $agent)
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                    <div>
                        <h4 style="font-weight: 700; margin: 0; font-size: 16px;">{{ $agent->name }}</h4>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Agent ID: {{ $agent->agent_id }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Brand ID: {{ $agent->brand_id }}</div>
                    </div>
                    @if($agent->environment === 'production')
                        <span style="background-color: #f0fdf4; color: #166534; font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: 700; border: 1px solid #bbf7d0;">PRODUCTION</span>
                    @else
                        <span style="background-color: #fffbeb; color: #b45309; font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: 700; border: 1px solid #fde68a;">SANDBOX</span>
                    @endif
                </div>

                <div style="background-color: #f1f5f9; padding: 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">WEBHOOK URL (COPY TO GOOGLE)</div>
                    <div style="font-family: monospace; font-size: 12px; color: var(--text-dark); word-break: break-all;">
                        {{ url('/api/rcs/webhook/' . $agent->agent_id) }}
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 16px;">
                    <span style="font-size: 12px; color: #166534; font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Active</span>
                    <form action="{{ route('rcs.settings.destroy', $agent->id) }}" method="POST" onsubmit="return confirm('Delete this agent? All associated templates and history will remain but you cannot send messages until reconfigured.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn" style="background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; border-radius: var(--radius-sm); padding: 6px 12px; font-size: 12px; font-weight: 600; cursor: pointer;">
                            <i class="fa-solid fa-trash"></i> Remove Agent
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <i class="fa-solid fa-robot" style="font-size: 40px; color: var(--text-light); margin-bottom: 16px;"></i>
                <h4 style="font-weight: 600; color: var(--text-main);">No RCS Agents Configured</h4>
                <p style="color: var(--text-muted); font-size: 14px;">Configure your first Google RCS Agent to start.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Agent Modal -->
<div id="addAgentModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:24px; width:100%; max-width:600px; max-height: 90vh; overflow-y: auto;">
        <h4 style="margin-top:0; font-weight:700; margin-bottom: 16px;">Configure RCS Agent</h4>
        <form action="{{ route('rcs.settings.store') }}" method="POST">
            @csrf
            
            <div class="grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                <div class="form-group">
                    <label class="form-label">Display Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Main Brand Agent" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Environment</label>
                    <select name="environment" class="form-control" required>
                        <option value="sandbox">Sandbox</option>
                        <option value="production">Production</option>
                    </select>
                </div>
            </div>

            <div class="grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 0;">
                <div class="form-group">
                    <label class="form-label">Brand ID</label>
                    <input type="text" name="brand_id" class="form-control" placeholder="Google RBM Brand ID" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Agent ID</label>
                    <input type="text" name="agent_id" class="form-control" placeholder="Google RBM Agent ID" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Service Account JSON (Secure)</label>
                <textarea name="service_account_json" class="form-control" rows="8" placeholder='{"type": "service_account", "project_id": "...", ...}' required style="font-family: monospace; font-size: 12px;"></textarea>
                <small style="color: var(--text-muted); font-size: 11px;">Paste the entire JSON contents of the Service Account key provided by Google Cloud Console. This is stored securely.</small>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <button type="button" class="btn" style="padding: 10px 16px; background:#f1f5f9; border:none; border-radius:6px; cursor:pointer;" onclick="document.getElementById('addAgentModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 10px 16px;"><i class="fa-solid fa-save"></i> Save Configuration</button>
            </div>
        </form>
    </div>
</div>
@endsection
