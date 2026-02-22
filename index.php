<?php
session_start();
include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_no  = trim($_POST['roll_no']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM voters WHERE roll_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // ✅ Store only required fields in session
           $_SESSION['user'] = [
    'id'         => $user['id'],
    'first_name' => $user['first_name'],
    'roll_no'    => $user['roll_no'],   // ✅ added
    'email'      => $user['email'],
    'department' => $user['department'],
    'has_voted'  => $user['has_voted']
];


            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Invalid Password!";
        }
    } else {
        $error = "❌ Register Number not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Voting - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body.bg-light {
        background-image: url('images/bg.jpeg'); /* put your image in /images/ */
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
        background: #0d6efd;
        color: #fff;
        padding: 10px;
        margin-bottom: 20px;
    }
    .moving-heading h3 {
        display: inline-block;
        padding-left: 100%;
        animation: moveText 12s linear infinite;
        font-weight: bold;
        margin: 0;
    }
    @keyframes moveText {
        from { transform: translateX(0); }
        to { transform: translateX(-100%); }
    }
    </style>
</head>
<body class="bg-light">

<div class="moving-heading">
    <h3>WELCOME TO MITM VOTING SYSTEM</h3>
</div>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4 rounded-4" style="width: 380px;">
        <h3 class="text-center mb-3"> Student Login</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Register Number</label>
                <input type="text" name="roll_no" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 rounded-pill">Login</button>
        </form>

        <p class="text-center mt-3 mb-0">
            New user? <a href="sign-up.php">Register here</a>
        </p>
    </div>
</div>

</body>
</html>
