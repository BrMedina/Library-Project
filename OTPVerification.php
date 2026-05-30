<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 w-25 border border-warning rounded p-5">
        <div class="row mb-5">
            <div class="col text-center fw-bold">
                <span class="display-4">OTP Verification</span>
            </div>
        </div>
        <div class="row my-3">
            <div class="col text-center fw-bold">
                <span class="text-warning h6">One time password (OTP) was sent to your email</span>
            </div>
        </div>

        <form action="otpverification.php" method="post">
            <div class="form-outline mb-4">
                <label class="form-label" for="form2Example1">Enter the OTP Number to verify</label>
                <input type="text" name="otp" id="form2Example1" class="form-control" required />
            </div>
            <input type="submit" name="ver" value="Verify" class="btn btn-warning btn-block w-100 mb-4">
        </form>
    </div>    

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

<?php

require_once 'dbconnection.php';
require_once 'VerifyOTPAddress.php';


if(isset($_POST['ver'])) {
    $userotp = $_POST['otp'];

    $otpsql = "Select * from user_table where otp = '".$userotp."'";
    $result = $conn->query($otpsql);


    if ($result->num_rows == 1) {
        $updatesql = "Update user_table set otp = NULL, 
        status = 'Active' where otp = '".$userotp."'";

        $conn->query($updatesql);

        ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Account Activated',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = "login.php";
            });
        </script>
        <?php
        

    } else {
        ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Invalid OTP Number',
                showConfirmButton: false,
                timer: 1500
            })
        </script>
        <?php
    }
}



?>