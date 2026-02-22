<?php
session_start();
include 'config.php';
include 'mail.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$user_id = $user['id'];
$email   = $user['email'];
$name    = $user['first_name'];

$message = "";

if ($user['has_voted'] == 1) {
    $message = "<div class='alert alert-success'>✅ You have already voted. Thank you!</div>";
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidate_id = $_POST['candidate_id'];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?");
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();

        $stmt2 = $conn->prepare("UPDATE voters SET has_voted = 1 WHERE id = ?");
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();

        $conn->commit();
        $_SESSION['user']['has_voted'] = 1;

        // ✅ Background thank-you email
        $subject = "Thank You for Voting - MITM Voting System";
        $body = "
            <p>Hi <strong>$name</strong>,</p>
            <p>Thank you for casting your vote in the <strong>MITM Voting System</strong>.</p>
            <p>Your participation is valuable and highly appreciated.</p>
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
                        ✅ Your vote has been recorded successfully! Thank you.
                    </div>";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "<div class='alert alert-danger'>❌ An error occurred while recording your vote. Please try again.</div>";
    }
}

// Fetch candidates
$candidates = $conn->prepare("SELECT * FROM candidates ORDER BY department, candidate_name");
$candidates->execute();
$result = $candidates->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Voting - Vote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 15px; }
        .btn-success { border-radius: 50px; }
        .btn-secondary { border-radius: 50px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">🏫 College Voting</a>
    <div class="d-flex">
      <a href="logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-4 text-center">🗳️ Cast Your Vote</h3>

        <?php if ($message) echo $message; ?>

        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm p-3">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['candidate_name']); ?></h5>
                        <p><strong>Branch:</strong> <?php echo htmlspecialchars($row['department']); ?></p>

                        <?php if ($_SESSION['user']['has_voted'] == 0): ?>
                            <form method="POST">
                                <input type="hidden" name="candidate_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-success w-100">Vote</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Voted</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

</body>
</html>
