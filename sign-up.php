<?php
session_start();
include 'config.php';
include 'mail.php';  // PHPMailer setup

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username   = trim($_POST['username']);
    $roll_no    = trim($_POST['roll_no']);
    $branch     = isset($_POST['branch']) ? $_POST['branch'] : '';
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (empty($branch)) {
        $message = "<div class='alert alert-danger'>⚠️ Please select MCA or MBA!</div>";
    } else {
        // Check if user already exists
        $check = $conn->prepare("SELECT * FROM voters WHERE roll_no = ? OR email = ?");
        $check->bind_param("ss", $roll_no, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>⚠️ This Register Number or Email is already registered!</div>";
        } else {
            // Insert new voter
            $sql = "INSERT INTO voters (first_name, roll_no, email, password, department, has_voted) 
                    VALUES (?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $username, $roll_no, $email, $password, $branch);

            if ($stmt->execute()) {
                // ✅ Background welcome email
                $subject = "Welcome to MITM Voting System";
                $body = "
                    <p>Hi <strong>$username</strong>,</p>
                    <p>Thank you for registering in <strong>MITM Voting System</strong>.</p>
                    <p>You can now log in using your Register Number and password to cast your vote.</p>
                    <p>Best Regards,<br>Voting Team</p>
                ";

                $cmd = "php send_email.php " 
                     . escapeshellarg($email) . " "
                     . escapeshellarg(base64_encode($subject)) . " "
                     . escapeshellarg(base64_encode($body));

                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    pclose(popen("start /B $cmd", "r")); // Windows
                } else {
                    exec($cmd . " > /dev/null 2>&1 &"); // Linux/Mac
                }

                $message = "<div class='alert alert-success'>
                                ✅ Registration successful! Thank you.<br>
                                <a href='index.php'>Login here</a>
                            </div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body.bg-light {
        background-image: url('images/bg.jpeg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
    .container { position: relative; z-index: 1; }
    .card { background-color: rgba(255, 255, 255, 0.9); border: none; }
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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Student Registration</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)) echo $message; ?>

                    <form method="POST" action="sign-up.php">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Register Number</label>
                            <input type="text" name="roll_no" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select name="branch" class="form-select" required>
                                <option value="">-- Select Branch --</option>
                                <option value="MCA">MCA</option>
                                <option value="MBA">MBA</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 rounded-pill">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
