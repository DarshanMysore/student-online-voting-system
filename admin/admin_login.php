<?php
session_start();

// Hardcoded admin credentials (you can also store in DB)
$admin_username = "darshan";
$admin_password = "123"; // change this to something strong

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body.bg-light {
        background-image: url('../images/bg.jpeg'); /* put your image in /images/ */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .container {
        position: relative;
        z-index: 1;
    }
    .card {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
    }
    .moving-heading {
        overflow: hidden;
        white-space: nowrap;
        background: #0d6efd; /* Bootstrap primary blue */
        color: #fff;
        padding: 10px;
        margin-bottom: 20px;
    }

    .moving-heading h3 {
        display: inline-block;
        padding-left: 100%; /* Start off-screen */
        animation: moveText 12s linear infinite;
        font-weight: bold;
        margin: 0;
    }
    @keyframes moveText {
        from {
            transform: translateX(0);
        }
        to {
            transform: translateX(-100%);
        }
    }
</style>

</head>
<body class="bg-light">
    <div class="moving-heading">
    <h3>WELCOME TO MITM VOTING SYSTEM</h3>
</div>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4 rounded-4" style="width: 380px;">
        <h2 class="text-center mb-3">WELCOME TO MITM</h2>
        <h5 class="text-center mb-3">Admin Login </h5>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 rounded-pill">Login</button>
        </form>
    </div>
</div>

</body>
</html>
