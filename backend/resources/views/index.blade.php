@use('Illuminate\Support\Facades\Vite')

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Frise Chronologique Interactive</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="m-0 p-3 flex justify-center items-center h-screen bg-gray-100 font-sans">

<!-- Notification -->
<x-notification />

<!-- Timeline -->
<x-timeline/>

</body>
</html>
