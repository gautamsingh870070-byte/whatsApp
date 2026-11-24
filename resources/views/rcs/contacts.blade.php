@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold" style="color: var(--text-dark);">RCS Contacts</h2>
        <button type="button" class="btn-primary" onclick="document.getElementById('addContactModal').style.display='flex'">
            <i class="fa-solid fa-plus"></i> Add Contact
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: #f8fafc; border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">Name</th>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">Phone Number</th>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">RCS Capable</th>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted); text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 16px 20px; font-size: 14px; font-weight: 600;">{{ $contact->name ?? 'Unknown' }}</td>
                            <td style="padding: 16px 20px; font-size: 14px;">{{ $contact->country_code }} {{ $contact->phone_number }}</td>
                            <td style="padding: 16px 20px;">
                                @if($contact->is_rcs_capable)
                                    <span style="background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Yes</span>
                                @else
                                    <span style="background-color: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Unknown</span>
                                @endif
                            </td>
                            <td style="padding: 16px 20px; text-align: right;">
                                <form action="{{ route('rcs.contacts.destroy', $contact->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this contact?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color: #ef4444; cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 30px; text-align: center; color: var(--text-muted);">No contacts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $contacts->links() }}
        </div>
    </div>
</div>

<!-- Add Contact Modal -->
<div id="addContactModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:24px; width:100%; max-width:400px;">
        <h4 style="margin-top:0; font-weight:700;">Add RCS Contact</h4>
        <form action="{{ route('rcs.contacts.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-top: 16px;">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe">
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="country_code" class="form-control" value="+91" style="width: 80px;" required>
                    <input type="text" name="phone_number" class="form-control" placeholder="10-digit Mobile Number" required>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <button type="button" class="btn" style="padding: 10px 16px; background:#f1f5f9; border:none; border-radius:6px; cursor:pointer;" onclick="document.getElementById('addContactModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-primary" style="padding: 10px 16px;"><i class="fa-solid fa-save"></i> Save Contact</button>
            </div>
        </form>
    </div>
</div>
@endsection
