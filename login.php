<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>  

    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row g-0 justify-content-center align-items-center w-100">
            <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center">
                <img src="./assets/logo2.png" alt="LibLogo" draggable="false">
            </div>

            <div class="col-lg-4 col-md-7 col-11">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="mb-4 text-center fw-bold mt-2">Login</h2>

                        <i class="bi bi-envelope"></i>
                        <label for="email" class="fw-bold">Email</label>
                        <input type="email" name="email" id="email" placeholder="johndoe@gmail.com" required class="form-control">

                        <i class="bi bi-lock"></i>
                        <label for="password" class="fw-bold mt-3">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" required class="form-control">

                        <div class="d-flex justify-content-end">
                            <button class="forgot-password mt-2">Forgot Password?</button>
                        </div>

                        <div class="mt-3">
                            <a href="index.php"><button type="submit" class="btn btn-primary btn-lg w-100 button">Login</button></a>
                            <hr>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <p>No Account? <a href="register.php"><button class="register-here">Register Here.</button></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="backgroundScript.js"></script>
</body>
</html>