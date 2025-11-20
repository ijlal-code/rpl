<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Arsip Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 0.75rem;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 767px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .table td, .table th {
                white-space: nowrap;
            }

            .card-header {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    @include('partials.navbar')

    <main class="flex-grow-1">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
