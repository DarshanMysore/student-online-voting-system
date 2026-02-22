<?php
session_start();
include '../config.php';  // adjust path if needed

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidate_name = $_POST['candidate_name'];
    $department = $_POST['department'];

    if (!empty($candidate_name) && !empty($department)) {
        // Check if candidate already exists
        $check = $conn->prepare("SELECT * FROM candidates WHERE candidate_name=? AND department=?");
        $check->bind_param("ss", $candidate_name, $department);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Candidate already exists in this department!</div>";
        } else {
            // Insert candidate with default vote_count = 0
            $stmt = $conn->prepare("INSERT INTO candidates (candidate_name, department, vote_count) VALUES (?, ?, 0)");
            $stmt->bind_param("ss", $candidate_name, $department);

            if ($stmt->execute()) {
                // ✅ Redirect to admin dashboard after success
                header("Location: admin_dashboard.php?msg=Candidate+Added+Successfully");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-warning'>All fields are required!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Candidate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center bg-primary text-white p-2">Add Candidate</h2>
        <?= $message ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Candidate Name</label>
                <input type="text" name="candidate_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="department" class="form-control" required>
                    <option value="">-- Select Branch --</option>
                    <option value="MCA">MCA</option>
                    <option value="MBA">MBA</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Add Candidate</button>
        </form>
    </div>
</div>
</body>
</html>
