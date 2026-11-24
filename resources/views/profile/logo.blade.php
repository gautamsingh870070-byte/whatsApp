@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Update App Logo</h2>
        <p style="color: #64748b; font-size: 14px;">Update the main logo of the application that appears in the top left sidebar.</p>
    </div>

    @if(session('success'))
        <div style="background-color: #dcfce7; color: #16a34a; padding: 12px 16px; border-radius: 8px; border: 1px solid #bbf7d0; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; border: 1px solid #fecaca; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">App Logo (Top Left Sidebar)</label>
                <div style="margin-bottom: 16px; padding: 16px; border: 1px dashed #cbd5e1; border-radius: 6px; text-align: center; background: #f8fafc;">
                    <img src="{{ asset((in_array(request()->getHost(), ['127.0.0.1', 'localhost']) ? '' : 'public/') . 'images/WABA.jpeg') }}?v={{ time() }}" alt="Current App Logo" style="max-width: 150px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                </div>
                <input type="file" name="app_logo" style="width: 100%; padding: 10px 14px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 14px; outline: none; transition: border-color 0.2s; background: #fff;" accept=".jpg,.jpeg,.png">
                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 6px;"><i class="fa-solid fa-circle-info"></i> Recommended size: Square aspect ratio.</small>
            </div>
            
            <!-- Hidden input to bypass name validation since we are using the same route -->
            <input type="hidden" name="name" value="{{ Auth::user()->name ?? 'Administrator' }}">

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary" style="background-color: #10b981; color: white; padding: 10px 20px; border-radius: 6px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s;">Save Logo</button>
            </div>
        </form>
    </div>
</div>
@endsection
