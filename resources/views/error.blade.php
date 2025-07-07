@extends('layouts.layout')

@section('content')
    <div class="main-container">
        <img src="{{ asset('uploads/Forward-only/Forward_Mail_logo_2024 1.png') }}" class="f-logo">
        
        <div class="error-container">
            <h1>Oops! Something went wrong</h1><br>
            <p>We encountered an issue while processing your request.</p><br>
            <p>Feel free to contact us at <b>carman@siol.net</b></p>
        </div>
    </div>

    <style>
        .error-container {
            text-align: center;
            padding: 50px;
            max-width: 600px;
            margin: auto;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
        }
        
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: #fff;
            background: #d9534f;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-back:hover {
            background: #c9302c;
        }
    </style>
@endsection
