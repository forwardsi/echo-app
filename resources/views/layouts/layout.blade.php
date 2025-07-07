<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nfc Apartments</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href='https://fonts.googleapis.com/css?family=Archivo Black' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Archivo' rel='stylesheet'>

         <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        
        <script src="{{ asset('js\Forms.js') }}"></script>
        <script src="{{ asset('js\manualCheck.js') }}"></script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    </head>
<body>

@yield('content')
<footer class="footer">
    <p>Powered by</p> 
    <a href="https://forward.si" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; color: inherit;">
        <img src="{{ asset('uploads/Forward-only/Forward-footer-logo.svg') }}" alt="Forward Logo" style="height:18px;">
    </a>
</footer>
</body>
</html>