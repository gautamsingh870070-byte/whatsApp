@extends('layouts.app')

@section('content')
<!-- CSRF Token for AJAX Requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .contacts-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-primary:hover {
        background-color: #20b858;
    }

    .contacts-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .card-header-tools {
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8fafc;
    }

    .search-wrapper {
        position: relative;
        width: 320px;
        max-width: 100%;
    }

    .search-wrapper i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
    }

    .search-wrapper input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }

    .search-wrapper input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .contacts-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .contacts-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .contacts-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 14px;
        color: #334155;
    }

    .contacts-table tr:hover {
        background-color: #f8fafc;
    }

    .contact-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .contact-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }

    .contact-name {
        font-weight: 600;
        color: #0f172a;
    }

    .contact-email {
        font-size: 12px;
        color: #64748b;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-saved {
        background-color: #dcfce7;
        color: #166534;
    }

    .badge-auto {
        background-color: #f1f5f9;
        color: #475569;
    }

    .action-btn {
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        padding: 6px;
        border-radius: 4px;
        transition: background 0.2s, color 0.2s;
    }

    .action-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .action-btn.delete:hover {
        background: #fee2e2;
        color: #ef4444;
    }

    .pagination-wrapper {
        padding: 16px 20px;
        border-top: 1px solid #e2e8f0;
        background-color: #f8fafc;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .custom-modal {
        background-color: #ffffff;
        border-radius: 12px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        transform: scale(0.95);
        transition: transform 0.2s ease;
        overflow: hidden;
    }

    .modal-overlay.active .custom-modal {
        transform: scale(1);
    }

    .modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 20px;
        color: #64748b;
        cursor: pointer;
    }

    .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }

    .form-input, .form-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-input:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
    }

    .modal-footer {
        padding: 18px 24px;
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-modal {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: none;
    }

    .btn-cancel {
        background-color: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-cancel:hover {
        background-color: #f1f5f9;
    }

    .btn-primary-save {
        background-color: var(--primary);
        color: #ffffff;
    }

    .btn-primary-save:hover {
        background-color: #20b858;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 16px;
    }
    .empty-state h3 {
        font-size: 18px;
        color: #334155;
        margin-bottom: 8px;
    }
    .empty-state p {
        color: #64748b;
        font-size: 14px;
    }
</style>

<div class="contacts-page-header">
    <div class="page-title">Contact Management</div>
    <div style="display: flex; gap: 12px;">
        @if(Auth::user()->hasPermission('contacts.create'))
        <button class="btn-primary" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" onclick="openImportModal()">
            <i class="fa-solid fa-upload"></i> Import CSV
        </button>
        @endif
        <a href="{{ route('contacts.export') }}" class="btn-primary" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;" target="_blank">
            <i class="fa-solid fa-download"></i> Export Contacts
        </a>
        @if(Auth::user()->hasPermission('contacts.create'))
        <button class="btn-primary" onclick="openContactModal()">
            <i class="fa-solid fa-plus"></i> Add New Contact
        </button>
        @endif
    </div>
</div>

<div class="contacts-card">
    <div class="card-header-tools">
        <form method="GET" action="{{ route('contacts.index') }}" class="search-wrapper" id="searchForm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" placeholder="Search by name, phone or email..." value="{{ request('search') }}" onsearch="document.getElementById('searchForm').submit()">
        </form>
    </div>

    <div class="table-responsive">
        <table class="contacts-table">
            <thead>
                <tr>
                    <th>Contact</th>
                    <th>Phone Number</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Added On</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr>
                    <td>
                        <div class="contact-name-cell">
                            @php
                                $initial = strtoupper(substr($contact->name ?: 'C', 0, 1));
                                if(!preg_match('/[A-Z0-9]/', $initial)) $initial = 'C';
                            @endphp
                            <div class="contact-avatar">{{ $initial }}</div>
                            <div>
                                <div class="contact-name">{{ $contact->name ?: 'Unknown' }}</div>
                                @if($contact->email)
                                <div class="contact-email">{{ $contact->email }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>+{{ $contact->phone }}</td>
                    <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $contact->notes }}">
                        {{ $contact->notes ?: '-' }}
                    </td>
                    <td>
                        @if($contact->is_manually_saved)
                            <span class="badge badge-saved"><i class="fa-solid fa-check"></i> Saved</span>
                        @else
                            <span class="badge badge-auto">Auto</span>
                        @endif
                    </td>
                    <td>{{ $contact->created_at->format('M d, Y') }}</td>
                    <td style="text-align: right; min-width: 140px;">
                        <button class="action-btn" title="Send Template" onclick="openSendTemplateModal('{{ $contact->phone }}', '{{ htmlspecialchars($contact->name, ENT_QUOTES) }}')">
                            <i class="fa-solid fa-paper-plane" style="color: var(--primary);"></i>
                        </button>
                        @if(!$contact->is_manually_saved && Auth::user()->hasPermission('contacts.create'))
                        <button class="action-btn" title="Save Contact" onclick="saveAutoContact({{ $contact->id }})">
                            <i class="fa-solid fa-user-plus" style="color: #3b82f6;"></i>
                        </button>
                        @endif
                        @if(Auth::user()->hasPermission('contacts.edit'))
                        <button class="action-btn" title="Edit Contact" onclick="editContact({{ $contact->id }}, '{{ htmlspecialchars($contact->name, ENT_QUOTES) }}', '{{ $contact->phone }}', '{{ htmlspecialchars($contact->email, ENT_QUOTES) }}', '{{ htmlspecialchars($contact->notes, ENT_QUOTES) }}')">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        @endif
                        @if(Auth::user()->hasPermission('contacts.delete'))
                        <button class="action-btn delete" title="Delete Contact" onclick="deleteContact({{ $contact->id }})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fa-solid fa-address-book"></i>
                            <h3>No contacts found</h3>
                            <p>You don't have any contacts matching your criteria.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contacts->hasPages())
    <div class="pagination-wrapper">
        {{ $contacts->links() }}
    </div>
    @endif
</div>

<!-- Add/Edit Contact Modal -->
<div class="modal-overlay" id="contactModal">
    <div class="custom-modal">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Add Contact</div>
            <button class="modal-close" onclick="closeContactModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form id="contactForm" onsubmit="submitContact(event)">
                <input type="hidden" id="contactId">
                <div class="form-group">
                    <label class="form-label">Phone Number (Required)</label>
                    <input type="text" id="contactPhone" class="form-input" required placeholder="Enter Phone Number">
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name (Required)</label>
                    <input type="text" id="contactName" class="form-input" required placeholder="Enter Name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address (Optional)</label>
                    <input type="email" id="contactEmail" class="form-input" placeholder="Enter Email">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea id="contactNotes" class="form-textarea" rows="3" placeholder="Enter Notes"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="closeContactModal()">Cancel</button>
            <button type="submit" form="contactForm" class="btn-modal btn-primary-save" id="btnSubmit">Save Contact</button>
        </div>
    </div>
</div>

<!-- Send Template Modal -->
<div class="modal-overlay" id="sendTemplateModal">
    <div class="custom-modal">
        <div class="modal-header">
            <div class="modal-title">Send Template Message</div>
            <button class="modal-close" onclick="closeSendTemplateModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form id="sendTemplateForm" onsubmit="submitSendTemplate(event)">
                <input type="hidden" id="stPhone">
                <div class="form-group">
                    <label class="form-label">To Contact</label>
                    <input type="text" id="stName" class="form-input" readonly style="background-color: #f8fafc;">
                </div>
                <div class="form-group">
                    <label class="form-label">Select Template</label>
                    <select id="stTemplate" class="form-input" required style="cursor: pointer;">
                        <option value="">Loading templates...</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="closeSendTemplateModal()">Cancel</button>
            <button type="submit" form="sendTemplateForm" class="btn-modal btn-primary-save" id="btnSendTemplate"><i class="fa-solid fa-paper-plane"></i> Send</button>
        </div>
    </div>
</div>

<!-- Import Contact Modal -->
<div class="modal-overlay" id="importContactModal">
    <div class="custom-modal">
        <div class="modal-header">
            <div class="modal-title">Import Contacts (CSV)</div>
            <button class="modal-close" onclick="closeImportModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 16px; font-size: 12px; color: #475569;">
                <strong>Expected CSV Format:</strong><br>
                Please ensure your CSV file contains the following columns in the exact order (headers optional but recommended):<br>
                <table style="width: 100%; margin-top: 8px; font-family: monospace; font-size: 11px;">
                    <tr style="font-weight: bold; border-bottom: 1px solid #cbd5e1;"><td>Name</td><td>Phone</td><td>Email</td><td>Notes</td></tr>
                    <tr><td>John Doe</td><td>+919876543210</td><td>john@example.com</td><td>VIP Customer</td></tr>
                </table>
            </div>
            <form id="importContactForm" onsubmit="submitImportContact(event)">
                <div class="form-group">
                    <label class="form-label">Upload CSV File</label>
                    <input type="file" id="importFile" name="file" class="form-input" accept=".csv" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="closeImportModal()">Cancel</button>
            <button type="submit" form="importContactForm" class="btn-modal btn-primary-save" id="btnSubmitImport"><i class="fa-solid fa-upload"></i> Import</button>
        </div>
    </div>
</div>

<script>
    function openContactModal() {
        document.getElementById('modalTitle').innerText = 'Add New Contact';
        document.getElementById('contactId').value = '';
        document.getElementById('contactForm').reset();
        document.getElementById('contactModal').classList.add('active');
    }

    function editContact(id, name, phone, email, notes) {
        document.getElementById('modalTitle').innerText = 'Edit Contact';
        document.getElementById('contactId').value = id;
        document.getElementById('contactName').value = name;
        document.getElementById('contactPhone').value = phone;
        document.getElementById('contactEmail').value = email;
        document.getElementById('contactNotes').value = notes;
        document.getElementById('contactModal').classList.add('active');
    }

    function closeContactModal() {
        document.getElementById('contactModal').classList.remove('active');
    }

    function submitContact(e) {
        e.preventDefault();
        
        const id = document.getElementById('contactId').value;
        const name = document.getElementById('contactName').value;
        const phone = document.getElementById('contactPhone').value;
        const email = document.getElementById('contactEmail').value;
        const notes = document.getElementById('contactNotes').value;
        
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerText = 'Saving...';

        const url = id ? `/contacts/${id}` : '{{ route("contacts.store") }}';
        const method = id ? 'PUT' : 'POST';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, phone, email, notes })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw data;
            }
            return data;
        })
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Save Contact';
            
            if (data.success) {
                closeContactModal();
                if (typeof showToast === 'function') {
                    showToast('Success', data.message, 'success');
                }
                setTimeout(() => window.location.reload(), 1000);
            } else {
                if (typeof showToast === 'function') {
                    showToast('Error', data.message || 'An error occurred', 'error');
                } else {
                    alert('Error: ' + (data.message || 'An error occurred'));
                }
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Save Contact';
            
            let errorMsg = 'Server communication failed.';
            if (err && err.errors) {
                // Laravel validation errors
                errorMsg = Object.values(err.errors).map(e => e.join('\n')).join('\n');
            } else if (err && err.message) {
                errorMsg = err.message;
            }

            if (typeof showToast === 'function') {
                showToast('Error', errorMsg, 'error');
            } else {
                alert('Error: ' + errorMsg);
            }
        });
    }

    function deleteContact(id) {
        if (!confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/contacts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Deleted', data.message, 'success');
                }
                setTimeout(() => window.location.reload(), 1000);
            } else {
                if (typeof showToast === 'function') {
                    showToast('Error', data.message, 'error');
                } else {
                    alert('Error: ' + data.message);
                }
            }
        })
        .catch(err => {
            if (typeof showToast === 'function') {
                showToast('Error', 'Server communication failed.', 'error');
            } else {
                alert('Server Error');
            }
        });
    }

    // Save Auto Contact
    function saveAutoContact(id) {
        if (!confirm('Are you sure you want to save this auto-contact?')) return;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/contacts/${id}/save`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') showToast('Saved', data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('Server Error'));
    }

    // Send Template Logic
    let templatesLoaded = false;
    let availableTemplates = [];

    function openSendTemplateModal(phone, name) {
        document.getElementById('stPhone').value = phone;
        document.getElementById('stName').value = name ? `${name} (+${phone})` : `+${phone}`;
        document.getElementById('sendTemplateModal').classList.add('active');
        
        if (!templatesLoaded) {
            fetchTemplates();
        } else {
            populateTemplateDropdown();
        }
    }

    function closeSendTemplateModal() {
        document.getElementById('sendTemplateModal').classList.remove('active');
    }

    function fetchTemplates() {
        fetch('{{ route("api.templates") }}')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.templates && Array.isArray(data.templates)) {
                availableTemplates = data.templates.filter(t => t.status === 'APPROVED');
                templatesLoaded = true;
                populateTemplateDropdown();
            } else {
                document.getElementById('stTemplate').innerHTML = '<option value="">Failed to load templates</option>';
            }
        })
        .catch(err => {
            document.getElementById('stTemplate').innerHTML = '<option value="">Failed to load templates</option>';
        });
    }

    function populateTemplateDropdown() {
        const select = document.getElementById('stTemplate');
        if (availableTemplates.length === 0) {
            select.innerHTML = '<option value="">No approved templates available</option>';
            return;
        }
        
        let html = '<option value="">-- Select an approved template --</option>';
        availableTemplates.forEach(t => {
            html += `<option value="${t.name}">${t.name} (${t.category}) - ${t.language}</option>`;
        });
        select.innerHTML = html;
    }

    function submitSendTemplate(e) {
        e.preventDefault();
        const phone = document.getElementById('stPhone').value;
        const templateName = document.getElementById('stTemplate').value;
        if (!templateName) {
            alert('Please select a template');
            return;
        }
        
        const btn = document.getElementById('btnSendTemplate');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData();
        formData.append('phone_number', phone);
        formData.append('template_name', templateName);

        fetch('{{ route("live.chat.send") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';
            if (data.success) {
                if (typeof showToast === 'function') showToast('Sent', 'Template sent successfully!', 'success');
                closeSendTemplateModal();
            } else {
                alert('Error: ' + (data.message || 'Failed to send template'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';
            alert('Server Error');
        });
    }

    // --- Import Contact Logic ---
    function openImportModal() {
        document.getElementById('importContactForm').reset();
        document.getElementById('importContactModal').classList.add('active');
    }

    function closeImportModal() {
        document.getElementById('importContactModal').classList.remove('active');
    }

    function submitImportContact(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('importFile');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Please select a file to import.');
            return;
        }

        const btn = document.getElementById('btnSubmitImport');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Importing...';

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("contacts.import") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-upload"></i> Import';
            
            if (data.success) {
                if (typeof showToast === 'function') showToast('Success', data.message, 'success');
                else alert(data.message);
                
                closeImportModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-upload"></i> Import';
            alert('Server Error during import.');
        });
    }
</script>
@endsection
