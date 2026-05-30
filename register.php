<?php
//for debugging purposes naka connect lang muna sa table remove require_once if done
require_once 'dbconnection.php';
require_once 'VerifyOTPAddress.php';

$swalScript = '';

if(isset($_POST['sub'])){

    $fname = ($_POST['fname']);
    $lname = ($_POST['lname']);
    $username = ($_POST['username']);
    $number = ($_POST['number']);
    $address = ($_POST['address']);
    $email = ($_POST['email']);
    $password = MD5($_POST['password']);

    $membername = $fname ." ". $lname;
    $role = "member";

    $imagepath = "imgStorage/".$_FILES['upload_img']['name'];
    copy($_FILES['upload_img']['tmp_name'], $imagepath);

    $otp = rand(000000,999999);
    $status = "Pending";
    $userTableCol = $number;

    $insertSql = "INSERT INTO user_table (full_name, role, username, password, email, user_tablecol, image_path, otp, status)
    VALUES ('$membername', '$role', '$username', '$password', '$email', '$userTableCol', '$imagepath', '$otp', '$status')";

    $res = $conn->query($insertSql);

    if ($res == true) {
        // Log member creation to member_table
        $memberLogSql = "INSERT INTO member_table (member_name, contact_information, address)
        VALUES ('$membername', '$number', '$address')";
        $conn->query($memberLogSql);

        $newUserId = $conn->insert_id;
        $logssql = "INSERT INTO logs_table (user_id, action, datetime) 
        VALUES ('" . $newUserId . "', 'Registered', NOW())";
        $conn->query($logssql);

        send_verification($membername, $email, $otp);
        $swalScript = "
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'OTPVerification.php';
            });
        </script>
        ";
    } else {
        $swalScript = "
        <script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Registration failed',
                text: 'Please try again.'
            });
        </script>
        ";
    }

};
?>

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
            <div class="col-lg-7 col-md-9 col-11">
                <div class="card shadow w-100" style="max-width: 900px; margin: 0 auto;">
                    <div class="card-body p-3">
                        <form method="POST" action="register.php" enctype="multipart/form-data">
                            <div class="text-center mb-2">
                                <img src="./assets/headerLogo.png" alt="Buenadvides Logo" draggable="false" style="height: 64px;">
                            </div>
                            <h2 class="mb-3 text-center fw-bold">Register</h2>

                            <div class="row g-2 align-items-start">
                                <div class="col-md-3 text-center">
                                    <label class="form-label fw-bold mb-1">Profile</label>
                                    <div>
                                        <img id="preview" src="./assets/emptyProfile.jpg" alt="uploadProfile" width="120" height="120" class="img-thumbnail mb-2">
                                    </div>
                                    <input type="file" name="upload_img" class="form-control form-control-sm" onchange="previewing(event);">
                                </div>

                                <div class="col-md-9">
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <label class="form-label mb-1">First Name</label>
                                            <input type="text" name="fname" id="fname" placeholder="John" required class="form-control">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label mb-1">Last Name</label>
                                            <input type="text" name="lname" id="lname" placeholder="Doe" required class="form-control">
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label mb-1">Username</label>
                                            <input type="text" name="username" id="username" placeholder="John Doe" required class="form-control">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label mb-1">Contact Number</label>
                                            <input type="tel" name="number" id="number" placeholder="09XXXXXXX" required class="form-control">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label mb-1">Address</label>
                                            <input type="text" name="address" id="address" placeholder="123 Marikina St." required class="form-control">
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="email" class="form-label mb-1">Email</label>
                                            <input type="email" name="email" id="email" placeholder="johndoe@gmail.com" required class="form-control">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="password" class="form-label mb-1">Password</label>
                                            <input type="password" name="password" id="password" placeholder="••••••••" required class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" name="sub" class="btn btn-primary btn-lg w-100 button">Register</button>
                                <hr>
                            </div>
                            <div class="d-flex justify-content-center mt-1">
                                <p>Already Have an Account? <a href="login.php" class="register-here">Login Here.</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="backgroundScript.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php echo $swalScript; ?>
</body>
</html>
