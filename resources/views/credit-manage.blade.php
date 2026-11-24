@extends('layouts.app')

@section('content')
<div class="page-title" style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
    <div class="icon-bg" style="background-color: #e8f5e9; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #2e7d32;">
        <i class="fa-solid fa-credit-card"></i>
    </div>
    <div>
        <h3 style="margin: 0; font-size: 18px; color: #333;">Credit Manage</h3>
        <p style="margin: 0; font-size: 13px; color: #777;">Allocate and manage user messaging credits</p>
    </div>
</div>

@if(session('success'))
<div style="background-color: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-weight: 500;">
    {{ session('success') }}
</div>
@endif

<div class="grid">
    <div class="card" style="flex-direction: column; align-items: flex-start;">
        <h4 style="margin: 0 0 16px 0; color: #555;">Total Network Limits</h4>
        <h2 style="margin: 0; font-size: 32px; color: #2e7d32;">{{ number_format($totalDailyLimits) }}</h2>
    </div>
    <div class="card" style="flex-direction: column; align-items: flex-start;">
        <h4 style="margin: 0 0 16px 0; color: #555;">Credits Assigned Total</h4>
        <h2 style="margin: 0; font-size: 32px; color: #d32f2f;">{{ number_format($totalCreditsAssigned) }}</h2>
    </div>
</div>

<div class="card" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 24px; margin-top: 24px; display: block;">
    <h4 style="margin: 0 0 24px 0; font-size: 16px; color: #333;">Assign Credits (Increase Daily Limit)</h4>
    @if(isset($remainingLimit) && Auth::user()->isAdmin())
    <div style="margin-bottom: 16px; padding: 12px; background: #e3f2fd; border-radius: 6px; color: #1976d2; font-weight: 600;">
        You have {{ $remainingLimit }} limits available to assign.
    </div>
    @endif
    
    <form action="{{ route('credit.manage.store') }}" method="POST">
        @csrf
        <div style="display: flex; gap: 16px; align-items: flex-end;">
            <div style="flex: 1;">
                <label class="form-label" style="display: block; margin-bottom: 8px; color: #555;">Select User</label>
                <select name="user_id" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="" disabled selected>-- Select a User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) - Current Limit: {{ $user->daily_limit }}</option>
                    @endforeach
                </select>
                @if($errors->has('user_id'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('user_id') }}</span>
                @endif
            </div>
            
            <div style="flex: 1;">
                <label class="form-label" style="display: block; margin-bottom: 8px; color: #555;">Credit Amount to Add</label>
                <input type="number" name="amount" class="form-control" placeholder="e.g. 500" required min="1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                @if($errors->has('amount'))
                    <span style="color: #d32f2f; font-size: 12px;">{{ $errors->first('amount') }}</span>
                @endif
            </div>
            
            <button type="submit" class="btn-primary" style="background-color: #2e7d32; padding: 12px 24px; color: white; border: none; border-radius: 4px; cursor: pointer;">
                <i class="fa-solid fa-plus"></i> Add Credits
            </button>
        </div>
    </form>
</div>
@endsection
