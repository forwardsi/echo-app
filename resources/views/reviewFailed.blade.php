@extends('layouts.layout')

@section('content')
<div class="main-container">
        <div class="failure-message">
            <!-- Adding the GIF on top of the failure message -->
            <div class="gif-container">
                <img src="{{ asset('uploads/Forward-only/coffee-fail-animation.gif') }}" alt="Fail Animation" class="gif-animation">
            </div>
            
            <h1 class="failure-header">We could not find your review.</h1>
            <p class="failure-description">Please go to the bartender and show him the posted review along with the following code:</p>
            
            <div class="failure-code-container">
                <h3 class="failure-code">#{{ $errorMessage }}</h3>
            </div>
        </div>
    </div>
@endsection