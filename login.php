<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-white rounded-2 d-flex align-items-center justify-content-center text-white me-3" style="width: 40px; height: 40px;">
                <img src="images/logo/logo.png" alt="Logo" style="width: 40px;">
            </div>
            <div>
                <div class="fw-bold text-dark lh-sm">Toko Obat Arah Aman</div>
                <div class="small text-muted">Login untuk masuk ke dashboard admin</div>
            </div>
        </div>
        <div class="mb-4 overflow-hidden rounded-3 border">
            <img src="images/hero/hero-image.jpeg" class="img-fluid rounded-3 border w-100" style="height: 100px; object-fit: cover;" alt="Dashboard Preview">
        </div>
        <h2 class="fw-bold text-dark mb-3">Login</h2>
        <form action="sv_login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                Login
            </button>
        </form>
        <div class="text-center mt-4 small text-muted">
            Baru disini? <a href="registration.php" class="text-primary text-decoration-none fw-medium">Buat akun sekarang</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>