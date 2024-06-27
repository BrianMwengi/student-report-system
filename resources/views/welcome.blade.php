<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Report System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="antialiased">
    <div class="relative flex items-center justify-center min-h-screen bg-cover bg-center" style="background-image: url('{{ asset('images/students.png') }}');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 text-center text-white p-6 bg-black bg-opacity-50 rounded-lg">
            <h1 class="text-5xl font-bold mb-4">Student Report System</h1>
            <p class="text-xl mb-6">A platform for managing student reports efficiently</p>
            <a href="/login" class="btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Get Started</a>
        </div>
    </div>
</body>
</html>
