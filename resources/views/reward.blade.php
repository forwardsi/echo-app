@extends('layouts.layout')

@section('content')
    <div class="main-container">
        <div class="container shadow">
            <h1 class="text-dark" style="margin-bottom:10px;">Show this QR code to the bartender and receive your reward</h1>

            @if (isset($qrCodeUrl))
                <div>
                    <img src="{{ $qrCodeUrl }}" alt="Freebie QR Code" class="img-fluid" style="max-width:100%;">
                </div>
            @else
                <p class="text-muted">Sorry, there was an issue retrieving your freebie QR code.</p>
            @endif
        </div>
    </div>
@endsection