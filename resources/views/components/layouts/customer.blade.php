<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Buku Menu Digital</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div class="max-w-md mx-auto bg-white min-h-screen relative shadow-2xl overflow-x-hidden">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
