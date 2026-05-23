<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>

    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="row g-0 justify-content-center align-items-center w-100">
            <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center">
                <img src="./assets/logo2.png" alt="LibLogo" draggable="false">
            </div>

            <div class="col-lg-4 col-md-7 col-11">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="mb-4 text-center fw-bold mt-2">Register</h2>


                        <div class="row mb-3">
                            <div class="col text-center">
                                <label for="" class="fw-bold">Profile Picture</label>
                                    <div class="col">
                                        <img id="preview" src="./assets/emptyProfile.jpg" alt="uploadProfile" width="150" height="150" class="img-thumbnail mb-3">
                                    </div>
                                    <div class="col">
                                        <label for="" class="fw-bold">Upload your Profile Picture</label>
                                        <input type="file" name="upload_img" id="" class="form-control" onchange="previewing(event);">
                                    </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col mb-3">
                                <label for="" class="fw-bold">First Name</label>
                                <input type="text" name="fname" id="fname" placeholder="John" required class="form-control">
                            </div>
                            <div class="col">
                                <label for="" class="fw-bold">Last Name</label>
                                <input type="text" name="lname" id="lname" placeholder="Doe" required class="form-control">
                            </div>
                        </div>
                        

                        <div class="row">
                            <div class="col mb-3">
                                <i class="bi bi-person"></i>
                                <label for="" class="fw-bold">Username</label>
                                <input type="text" name="username" id="username" placeholder="John Doe" required class="form-control">
                            </div>
                            <div class="col">
                                <i class="bi bi-telephone"></i>
                                <label for="" class="fw-bold">Contact Number</label>
                                <input type="tel" name="number" id="number" placeholder="09XXXXXXX" required class="form-control">
                            </div>
                        </div>
                        
                        
                        <i class="bi bi-envelope"></i>
                        <label for="email" class="fw-bold">Email</label>
                        <input type="email" name="email" id="email" placeholder="johndoe@gmail.com" required class="form-control bi bi-envelope">

                        <i class="bi bi-lock"></i>
                        <label for="password" class="fw-bold mt-3">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" required class="form-control">



                        <div class="mt-3">
                            <button type="submit" name="sub" class="btn btn-primary btn-lg w-100 button">Register</button>
                            <hr>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <p>Already Have an Account? <a href="login.php"><button class="register-here">Login Here.</button></a> </p>
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



<?php


if(isset($_POST['sub']))

    $fname = ($_POST['fname']);
    $lname = ($_POST['lname']);
    $username = ($_POST['username']);
    $number = ($_POST['number']);
    $email = ($_POST['email']);
    $password = MD5($_POST['password']);

    $membername = $fname ." ". $lname;

    $imagepath = "./imgStorage".$_FILES['upload_img']['name'];
    copy($_FILES['upload_img']['tmp_name'], $imagepath);

    $otp = rand(000000,999999);

    
    
    
?>
