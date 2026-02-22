<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Voting - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"> Mitm College Voting</a>
    <div class="d-flex">
      <a href="logout.php" class="btn btn-danger btn-sm"> Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="card shadow p-4 rounded-4">
        <h3 class="mb-3"> Welcome, <?php echo $user['first_name']; ?>!</h3>
        <p><strong>Register Number:</strong> <?php echo $user['roll_no']; ?></p>
        <p><strong>Branch:</strong> <?php echo $user['department']; ?></p>

        <?php if ($user['has_voted'] == 1): ?>
            <div class="alert alert-success"> You have already voted. Thank you!</div>
        <?php else: ?>
            <div class="alert alert-warning"> You have not voted yet.</div>
            <a href="vote.php" class="btn btn-success btn-lg rounded-pill">click to Vote</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
