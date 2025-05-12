<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Surat')</title>
    <link rel="icon" href="{{ asset('images/logo-eximbank.png') }}" type="image/png">

    <!-- Bootstrap CSS manual -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    {{-- Navbar dari partial --}}
    @include('partials.navbar')

    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Bootstrap JS manual -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
