@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold" style="color: var(--text-dark);">RCS Message History</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 16px; font-weight: 700; margin: 0;">Recent Messages</h4>
        </div>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: #f8fafc; border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">Date & Time</th>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">Customer</th>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">Agent</th>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">Message</th>
                        <th style="padding: 12px 20px; font-weight: 600; font-size: 13px; color: var(--text-muted);">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $msg)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 16px 20px; font-size: 14px;">{{ $msg->created_at->format('d M Y, H:i') }}</td>
                            <td style="padding: 16px 20px; font-size: 14px; font-weight: 600;">{{ $msg->customer_number }}</td>
                            <td style="padding: 16px 20px; font-size: 14px;">{{ $msg->agent->name ?? 'N/A' }}</td>
                            <td style="padding: 16px 20px; font-size: 13px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ is_string($msg->content) ? $msg->content : json_encode($msg->content) }}
                            </td>
                            <td style="padding: 16px 20px;">
                                @if($msg->status === 'sent')
                                    <span style="background-color: #eff6ff; color: #1d4ed8; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Sent</span>
                                @elseif($msg->status === 'delivered')
                                    <span style="background-color: #f0fdf4; color: #15803d; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">Delivered</span>
                                @elseif($msg->status === 'read')
                                    <span style="background-color: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;"><i class="fa-solid fa-check-double"></i> Read</span>
                                @elseif($msg->status === 'failed')
                                    <span style="background-color: #fef2f2; color: #b91c1c; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;" title="{{ $msg->failure_reason }}">Failed</span>
                                @else
                                    <span style="background-color: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">{{ ucfirst($msg->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 30px; text-align: center; color: var(--text-muted);">No messages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
