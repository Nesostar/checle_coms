<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checle Online Management System [COMS]</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
    background-color: #f7f7f7; 
    font-family: Arial, sans-serif; 
}

/* Header */
.header-image {
    width: 100%;
    height: 100px;
    overflow: hidden;
}

.header-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Main Box */
.container-box {
    background: #fff;
    margin: 30px auto;
    padding: 20px;
    border-radius: 8px;
    max-width: 1000px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Login section */
.login-box {
    border-left: 2px solid #ddd;
    padding-left: 30px;
}

.form-control {
    border-radius: 0;
}

.btn-login {
    background-color: #006633;
    color: white;
    width: 100%;
}

.btn-login:hover {
    background-color: #004d26;
}

.title {
    color: #cc0000;
    font-weight: bold;
}

.footer {
    text-align: center;
    margin-top: 20px;
    color: #666;
}

/* =========================
   MOBILE RESPONSIVE FIX
========================= */
@media (max-width: 768px) {

    .header-image {
        height: 26.5px;
        width: 100%;
    }

    .container-box {
        margin: 15px;
        padding: 15px;
    }

    .login-box {
        border-left: none;
        padding-left: 0;
        margin-top: 20px;
    }

    .container-box .col-md-7,
    .container-box .col-md-5 {
        text-align: center;
    }

    .container-box img {
        width: 100%;
        max-width: 300px;
        height: auto;
    }

    h6 {
        font-size: 14px;
    }

    .footer {
        font-size: 12px;
        padding: 10px;
    }
}

@media (max-width: 768px) {
    .col-md-7,
    .col-md-5 {
        width: 100%;
    }
}



    </style>
     <!-- Cache-busting CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
</head>
<body>

    <!-- Thin Modern Header -->
    <div class="header-image">
        <img src="{{ asset('images/banner.jpg') }}" alt="Checle Online Management System Header" class="img-fluid w-100">
    </div>

    <!-- Main Content --><div class="container-box">
    <div class="row align-items-center">
        <div class="col-md-7 text-center mb-4 mb-md-0">
            <h6 class="title">Welcome: Checle Online Management System [COMS]</h6>
            <img src="{{ asset('images/workers.png') }}" class="img-fluid mt-3">
        </div>

        <div class="col-md-5 login-box">
            <h6 class="text-center mb-3">LOGIN [ COMS ]</h6>
            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Enter Username" name="username" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" placeholder="Enter Password" name="password" required>
                </div>
                <div class="mb-3">
                    <select class="form-control" name="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin / Director / Manager</option>
                        <option value="staff">Cashier</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-login">Login</button>
                <div class="mt-3 text-center">
                    <small>Forget Your Password? <a href="{{ route('password.request') }}">Click Here to Reset</a>
</small>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} Checle General Traders Co. LTD. All rights reserved.</p>
    </div>
<!-- Cache-busting JS -->
    <script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>
</body>
</html>
