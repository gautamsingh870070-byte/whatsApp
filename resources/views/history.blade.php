@extends('layouts.app')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        padding: 16px 24px;
        border-radius: 8px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .title-section {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .title-icon {
        background-color: #e3f2fd;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #2196f3;
    }
    
    .title-text h3 {
        margin: 0;
        color: #333;
        font-size: 18px;
    }
    
    .title-text p {
        margin: 4px 0 0 0;
        color: #d32f2f;
        font-size: 13px;
        font-weight: 600;
    }

    .date-filter {
        background-color: #26a69a;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Table Container */
    .table-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 24px;
    }

    .table-controls {
        display: flex;
        justify-content: space-between;
        margin-bottom: 16px;
        font-size: 14px;
        color: #555;
    }
    .table-controls select {
        padding: 4px 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
        font-size: 13px;
    }

    th {
        text-align: left;
        padding: 12px 16px;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        color: #333;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
    }

    td {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        color: #555;
    }

    .status-success { color: #2e7d32; font-weight: 600; }
    .status-failed { color: #c62828; font-weight: 600; }

    .empty-state {
        text-align: center;
        background-color: #f5f5f5;
        padding: 16px;
        color: #777;
    }

    /* Pagination */
    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #777;
        margin-top: 16px;
    }
    
    .pagination-links nav {
        display: flex;
    }

    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 4px;
        margin: 0;
    }
    .page-item.disabled .page-link, .page-item.disabled span {
        color: #999;
        pointer-events: none;
        background-color: #fff;
        border-color: #ddd;
    }
    .page-item.active .page-link, .page-item.active span {
        z-index: 3;
        color: #fff;
        background-color: #26a69a;
        border-color: #26a69a;
    }
    .page-link, .pagination span {
        position: relative;
        display: block;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.25;
        color: #26a69a;
        background-color: #fff;
        border: 1px solid #ddd;
        text-decoration: none;
    }
    .page-link:hover {
        background-color: #f5f5f5;
        color: #00897b;
    }
    .page-item:first-child .page-link, .page-item:first-child span {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .page-item:last-child .page-link, .page-item:last-child span {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
</style>

<!-- Header -->
<div class="page-header">
    <div class="title-section">
        <div class="title-icon">
            <i class="fa-regular fa-clipboard"></i>
        </div>
        <div class="title-text">
            <h3>Reports</h3>
            <p>(Note: Reports Available for Last 30 Days)</p>
        </div>
    </div>
    <div class="date-filter">
        {{ request('start_date', now()->format('F d, Y')) }} - {{ request('end_date', now()->format('F d, Y')) }}
    </div>
</div>

<!-- Table Card -->
<div class="table-card">
    <div class="table-controls">
        <div>
            <select>
                <option>10</option>
                <option>25</option>
                <option>50</option>
            </select>
            entries per page
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Campname</th>
                <th>Number</th>
                <th>Message</th>
                <th>Status</th>
                <th>Submit Date</th>
                <th>Download</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->template_name ?? 'N/A' }}</td>
                    <td>{{ $log->phone_number }}</td>
                    <td>{{ ucfirst($log->message_type) }}</td>
                    <td class="{{ $log->status == 'success' ? 'status-success' : 'status-failed' }}">{{ ucfirst($log->status) }}</td>
                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($log->api_response)
                            <a href="#" style="color: #4a90e2; text-decoration: none;"><i class="fa-solid fa-download"></i> Log</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">No data available in table</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap">
        <div>
            Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
        </div>
        <div class="pagination-links">
            {{ $logs->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
