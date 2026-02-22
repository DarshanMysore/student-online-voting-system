<?php
session_start();
include '../config.php';  // FIXED PATH

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";

// Handle delete voter
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM voters WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Voter deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
    }
}

// Handle reset vote status
if (isset($_GET['reset'])) {
    $id = intval($_GET['reset']);
    $sql = "UPDATE voters SET has_voted = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-info'>🔄 Voter vote status reset successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
    }
}

// Fetch voters
$sql = "SELECT * FROM voters ORDER BY department, roll_no";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voters - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- 🔹 Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin_dashboard.php">⚙️ Admin Panel</a>
    <div class="d-flex">
      <!-- 🏠 Home Button -->
      <a href="admin_dashboard.php" class="btn btn-light btn-sm me-2">🏠 Home</a>
      <a href="admin_manage_candidates.php" class="btn btn-success btn-sm me-2">🗳️ Manage Candidates</a>
      <a href="admin_dashboard.php" class="btn btn-info btn-sm me-2">📊 View Results</a>
      <a href="admin_logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="card shadow p-4 rounded-4">
        <h3 class="mb-3">🗂️ Manage Voters</h3>

        <?php if ($message) echo $message; ?>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Register Number</th>
                    <th>Branch</th>
                    <th>Voted?</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td>
                                <?php if ($row['has_voted'] == 1): ?>
                                    <span class="badge bg-success">Yes</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="admin_manage_voters.php?reset=<?php echo $row['id']; ?>" 
                                   class="btn btn-warning btn-sm"
                                   onclick="return confirm('Reset this voter’s vote status?')">🔄 Reset</a>

                                <a href="admin_manage_voters.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this voter?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No voters found!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
