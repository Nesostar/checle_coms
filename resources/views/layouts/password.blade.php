<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/image.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/image.png') }}">
    <title>{{ $title ?? 'COMS' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* =========================
   RESPONSIVE SIDEBAR FIX
========================= */
@media (max-width: 768px) {

  body {
    overflow-x: hidden;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: -260px; /* hidden by default */
    width: 240px;
    height: 100vh;
    transition: left 0.3s ease;
    z-index: 9999;
  }

  .sidebar.show {
    left: 0;
  }

  .main {
    margin-left: 0 !important;
    padding: 10px;
  }
}

    </style>
     <!-- Cache-busting CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
</head>
<body style="background:#f5f5f5;">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    {{ $title ?? 'COMS' }}
                </div>
                <div class="card-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Cache-busting JS -->
    <script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
</body>
</html>
