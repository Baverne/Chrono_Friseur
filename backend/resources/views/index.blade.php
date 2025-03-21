<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Frise Chronologique Interactive</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <script defer src="https://unpkg.com/@alpinejs/ui@3.14.9/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/focus@3.14.9/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="flatpickr.min.css">
    <script src="flatpickr.js"></script>
</head>

<body class="m-0 p-0 flex justify-center items-center h-screen bg-gray-100 font-sans">

<!-- Notification -->
<x-notification />

<!-- AddEvent -->
<x-add-event/>

<!-- Timeline -->
<div class="flex flex-col items-center">
    <div id="timeline" class="border border-gray-300 bg-white relative"></div>
</div>

<!-- scripts -->
<script src="https://d3js.org/d3.v7.min.js"></script>

<script src="app.js"></script>
<script src="datetime-format.js"></script>
</body>
</html>
