<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Julee&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            background: #10141B;
            font-family: 'Segoe UI', sans-serif;

            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        /* movie card */

        .section-title {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .card-movie {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
            transition: .3s;
        }

        .card-movie:hover {
            transform: translateY(-6px);
        }

        .btn-book {
            background: #0d6efd;
            color: white;
            border-radius: 8px;
            padding: 6px 14px;
        }
    </style>

</head>

<body>
{{-- Navbar --}}
@include('layouts.navbar')

<main>
    @yield('content')
</main>

{{-- Footer --}}
@include('layouts.footer')

</body>

</html>
