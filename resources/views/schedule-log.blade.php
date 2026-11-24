@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div class="icon-bg" style="background-color: #f3e5f5; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #7b1fa2;">
        <i class="fa-solid fa-clipboard-list"></i>
    </div>
    <div>
        <h3 style="margin: 0; font-size: 18px; color: #333;">Schedule Log</h3>
        <p style="margin: 0; font-size: 13px; color: #777;">View your upcoming and past scheduled campaigns</p>
    </div>
</div>

@if(session('success'))
<div style="background-color: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-weight: 500;">
    {{ session('success') }}
</div>
@endif

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h4 style="margin: 0; font-size: 16px; color: #333;">Scheduled Messages</h4>
    </div>
    
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Scheduled For</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Campaign / User</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Target Numbers</th>
                <th style="padding: 12px 16px; font-weight: 600; color: #555;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $schedule)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px 16px; font-weight: 500; color: #333;">{{ $schedule->scheduled_at->format('M d, Y h:i A') }}</td>
                <td style="padding: 12px 16px; color: #333;">
                    <strong>{{ $schedule->campaign_name }}</strong><br>
                    <span style="font-size: 12px; color: #666;">By: {{ $schedule->user->name }}</span>
                </td>
                <td style="padding: 12px 16px; color: #666; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $schedule->phone_numbers }}">
                    {{ Str::limit($schedule->phone_numbers, 25) }}
                </td>
                <td style="padding: 12px 16px;">
                    @if($schedule->status == 'pending')
                        <span style="background: #fff3e0; color: #f57c00; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Pending</span>
                    @elseif($schedule->status == 'processing')
                        <span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Processing</span>
                    @elseif($schedule->status == 'completed')
                        <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Completed</span>
                    @else
                        <span style="background: #ffebee; color: #c62828; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Failed</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding: 40px; text-align: center; color: #999;">
                    <i class="fa-regular fa-calendar-xmark" style="font-size: 32px; margin-bottom: 12px; color: #ddd;"></i>
                    <br>No scheduled messages found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
