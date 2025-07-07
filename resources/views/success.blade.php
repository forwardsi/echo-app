@extends('layouts.layout')

@section('content')
    
<div class="main-container">
    @if (isset($reward) && isset($email))
        <!-- Message for successful review confirmation -->
        <div class="success-message">
            <h1 style="margin-bottom:20px">Review was confirmed.</h1>
            <p>The <strong>{{ $reward }}</strong> was successfully used and sent to <strong>{{ $email }}</strong>.</p>
            <button onclick="window.location.href='{{ url()->previous() }}'" style="margin-top:20px; padding:10px 20px; background-color:#4CAF50; color:white; border:none; border-radius:5px; cursor:pointer;">
                Go Back
            </button>
        </div>
    @elseif (isset($qrCodeUrl))
        <!-- Message and display for QR code -->
        <div class="qr-code-message">
            <h1>QR Code</h1>
            <img src="{{ $qrCodeUrl }}" alt="QR Code" class="qr-code-image">
        </div>
    @else
        <!-- Fallback message if no data is available -->
        <div class="error-message">
            <h1>Something went wrong.</h1>
            <p>Unable to retrieve the requested information.</p>
        </div>
    @endif
</div>
@endsection
