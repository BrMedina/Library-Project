<?php
require_once 'dbconnection.php';
session_start();

$swalScript = '';

// button func
if(isset($_POST['sub'])) {
    //user input
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $loginsql = "Select * from user_table where username = '" . $username ."' and password = '". $password ."' and status ='Active'";
    
    $result = $conn->query($loginsql);

    //check if there is a match record
    if ($result->num_rows == 1) {


        $fieldname = $result -> fetch_assoc();
        
        $fullname = $fieldname['full_name'];
        $usertype = $fieldname['role'];
        $id = $fieldname['user_id'];

        $membersql = "Select member_id from member_table where member_name = '" . $fullname ."'";
        $resultmember = $conn->query($membersql);
        $memberidfield = $resultmember -> fetch_assoc();

        $memberId = $memberidfield['member_id'];

        //session variable
        $_SESSION['user_type'] = $usertype;
        $_SESSION['fullname'] = $fullname;
        $_SESSION['id'] = $id;
        $_SESSION['memberid'] = $memberId;

        $logssql = "INSERT INTO logs_table (user_id, action, datetime) 
        VALUES ('" . $_SESSION['id'] . "', 'Logged In', NOW())";
        $conn->query($logssql);

        //success alert and redirect
        if ($usertype === 'Administrator') {
            $redirectUrl = 'admindashboard.php';
        } elseif ($usertype === 'Librarian') {
            $redirectUrl = 'librariandashboard.php';
        } else {
            $redirectUrl = 'index.php';
        }

        $swalScript = "
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Login Successful',
                text: 'Welcome " . $fullname . "',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '" . $redirectUrl . "';
            });
        </script>
        ";
    } else {
        //invalid credentials
        $swalScript = "
        <script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Invalid Account',
                text: 'Username or password is incorrect',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
        ";
    }
}
?>

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
<form action="login.php" method=post>
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
                        <label for="username" class="fw-bold">Username</label>
                        <input type="text" name="username" id="username" placeholder="johndoe" required class="form-control">

                        <i class="bi bi-lock"></i>
                        <label for="password" class="fw-bold mt-3">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" required class="form-control">

                        <div class="d-flex justify-content-end">
                            <button class="forgot-password mt-2">Forgot Password?</button>
                        </div>

                        <div class="mt-3">
                            <button type="submit" name="sub" class="btn btn-primary btn-lg w-100 button">Login</button>
                            <hr>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <p>No Account? <a href="register.php" class="register-here">Register Here.</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="backgroundScript.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php echo $swalScript; ?>
</body>
</html>