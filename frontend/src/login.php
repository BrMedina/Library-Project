<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>

    <div class="container-lg container-md py-5 d-flex justify-content-center flex-column h-100">
        <div class="row g-0 justify-content-center">
            <div class="col-lg-6 d-none d-lg-block">
                <div class="d-flex flex-column justify-content-center h-100">
                    <div class="d-flex justify-contnet-center">
                        <img src="../assets/logo2.png" alt="LibLogo" width="800px" height="200px">
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-7 col-12">
                <div class="card shadow">
                    <div class="card-body p5">
                        <h2 class="mb-4 text-center fw-bold mt-3">Login</h2>

                        <label for="" class="fw-bold">Email</label>
                        <input type="email" name="email" id="email" placeholder="johndoe@gmail.com" required class="form-control">

                        <label for="" class="fw-bold mt-3">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" required class="form-control">

                        <div class="row">
                            <button class="forgot-password mt-2">Forgot Password?</button>
                        </div>
                        

                        <div class="row px-3 mt-3">
                            <button class="btn btn-primary btn-lg btn-block">Login</button>
                        </div>



                    </div>
                </div>

            </div>
        </div>
    </div>
    
</body>
</html>