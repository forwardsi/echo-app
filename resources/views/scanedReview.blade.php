@extends('layouts.layout')

@section('content')
<div class="main-container">
    @if (session('status') === 'success')
        <!-- Success Message -->
        <div class="failure-message">
            <h1 class="failure-header">Successfully Scanned</h1>
            <p class="failure-description">{{ session('message') }}</p>
        </div>
    @elseif (session('status') === 'error')
        <!-- Error Message -->
        <div class="failure-message">
            <h1 class="failure-header">Error</h1>
            <p class="failure-description">{{ session('message') }}</p>
        </div>
    @else
        <!-- Fallback Message -->
        <div class="failure-message">
            <h1 class="failure-header">Oops!</h1>
            <p class="failure-description">Something unexpected happened.</p>
        </div>
    @endif
</div>
@endsection