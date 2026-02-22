<?php
session_start();
include '../config.php';  // FIXED PATH

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";

// Handle delete candidate
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Candidate deleted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: " . $stmt->error . "</div>";
    }
}

// Fetch candidates
$sql = "SELECT * FROM candidates ORDER BY department, candidate_name";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Candidates - Admin Panel</title>
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
      <a href="admin_add_candidate.php" class="btn btn-success btn-sm me-2">➕ Add Candidate</a>
      <a href="admin_dashboard.php" class="btn btn-info btn-sm me-2">📊 View Results</a>
      <a href="admin_logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="card shadow p-4 rounded-4">
        <h3 class="mb-3">🗂️ Manage Candidates</h3>

        <?php if ($message) echo $message; ?>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Candidate Name</th>
                    <th>Branch</th>
                    <th>Votes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo $row['vote_count']; ?></td>
                            <td>
                                <a href="admin_manage_candidates.php?delete=<?php echo $row['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this candidate?')">
                                   🗑️ Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No candidates found!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
