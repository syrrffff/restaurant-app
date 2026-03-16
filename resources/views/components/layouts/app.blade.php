<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Restoran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @livewireStyles
</head>
<body>

    <div class="flex-h-screen">

        @include('components.sidebar')

        <div class="content-area" style="display: flex; flex-direction: column; flex: 1;">

            @include('components.header')

            <main class="main-content" style="flex: 1;">
                {{ $slot }}
            </main>

            @include('components.footer')

        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
    @livewireScripts
</body>
</html>
