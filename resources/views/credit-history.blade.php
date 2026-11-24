@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div class="icon-bg" style="background-color: #fff3e0; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #f57c00;">
        <i class="fa-solid fa-clock-rotate-left"></i>
    </div>
    <div>
        <h3 style="margin: 0; font-size: 18px; color: #333;">Credit History</h3>
        <p style="margin: 0; font-size: 13px; color: #777;">View transaction history of credit allocations</p>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 24px;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Date & Time</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">User</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Action</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Amount</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Assigned By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $history)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px 16px; color: #666;">{{ $history->created_at->format('M d, Y h:i A') }}</td>
                <td style="padding: 12px 16px; font-weight: 500; color: #333;">{{ $history->user->name }} <br><span style="font-size: 12px; color: #888; font-weight: normal;">{{ $history->user->email }}</span></td>
                <td style="padding: 12px 16px;">
                    @if($history->action == 'add')
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Added</span>
                    @else
                        <span style="background: #ffebee; color: #c62828; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Subtracted</span>
                    @endif
                </td>
                <td style="padding: 12px 16px; font-weight: bold; color: {{ $history->action == 'add' ? '#2e7d32' : '#c62828' }};">
                    {{ $history->action == 'add' ? '+' : '-' }}{{ number_format($history->amount) }}
                </td>
                <td style="padding: 12px 16px; color: #666;">{{ $history->assigned_by }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 40px; text-align: center; color: #999;">
                    <i class="fa-solid fa-file-invoice" style="font-size: 32px; margin-bottom: 12px; color: #ddd;"></i>
                    <br>No credit history found. Assign credits to see them here.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
