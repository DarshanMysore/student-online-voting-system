<?php
session_start();
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch results
$sql = "SELECT department, candidate_name, vote_count FROM candidates ORDER BY department, vote_count DESC";
$result = $conn->query($sql);

$data = [];
$branches = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    $branches[$row['department']] = true;
}
$result->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
    background-color: #f4f7fb;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: all 0.3s ease;
}

/* College Header */
.college-header {
    width: 100%;
    background: #062442;
    color: white;
    padding: 12px 20px;
    font-size: 20px;
    font-weight: bold;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1100;
    display: flex;
    justify-content: space-between;
    align-items: center;
    overflow: hidden;
}

.college-header span.text {
    display: inline-block;
    white-space: nowrap;
    padding-left: 100%;
    animation: scrollText 20s linear infinite;
}

@keyframes scrollText {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.menu-btn {
    font-size: 24px;
    cursor: pointer;
}

/* Sidebar */
.sidebar {
    width: 220px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 55px;
    background: #062442;
    color: #fff;
    padding-top: 20px;
    box-shadow: 3px 0 10px rgba(0,0,0,0.2);
    transition: width 0.3s ease;
    overflow: hidden;
    z-index: 1000;
}

.sidebar h4 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: bold;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    margin: 8px 15px;
    text-decoration: none;
    color: #ecf0f1;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sidebar a:hover {
    background: #1abc9c;
    color: #fff;
    transform: translateX(5px);
}

.sidebar.collapsed { width: 70px; }
.sidebar.collapsed a span,
.sidebar.collapsed h4 span { display: none; }

/* Main Content */
.main-content {
    margin-left: 220px;
    padding: 30px;
    transition: margin-left 0.3s ease;
    margin-top: 80px;
}

.main-content.expanded { margin-left: 70px; }

/* Page Header */
.page-header h2 {
    font-size: 40px;
    font-weight: bold;
    color: #051e45;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.page-header hr {
    border: 2px solid #14b8a6;
    width: 120px;
    margin: 10px auto 0;
    border-radius: 3px;
}

/* Dashboard Cards */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.card-box {
    padding: 20px;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    text-align: center;
    transition: transform 0.3s ease;
}
.card-box:hover { transform: translateY(-5px); }
.card-box h5 { margin-bottom: 10px; color: #2c3e50; }
.card-box span { font-size: 24px; font-weight: bold; color: #1abc9c; }

/* Results Section */
.results-section {
    border-radius: 15px;
    background: #fff;
    padding: 25px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
}
.results-section h3 { margin-bottom: 20px; color: #2c3e50; font-weight: bold; }
.table { border-radius: 10px; overflow: hidden; }
.table thead { background: #2c3e50; color: #fff; }

/* Chart */
.chart-container { width: 100%; max-width: 800px; aspect-ratio: 2 / 1; margin: 40px auto; }
</style>
</head>
<body>
<!-- Header -->
<div class="college-header">
    <span class="text">Department of MCA Maharaja Institute of Technology</span>
    <span class="menu-btn" id="menuBtn">☰</span>
</div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h4><i class="bi bi-speedometer2"></i> <span>Admin Panel</span></h4>
    <a href="admin_dashboard.php"><i class="bi bi-house"></i> <span>Dashboard</span></a>
    <a href="admin_add_candidate.php"><i class="bi bi-person-plus"></i> <span>Add Candidate</span></a>
    <a href="admin_manage_candidates.php"><i class="bi bi-people"></i> <span>Manage Candidates</span></a>
    <a href="admin_manage_voters.php"><i class="bi bi-person-badge"></i> <span>Manage Voters</span></a>
    <a href="export_results_pdf.php"><i class="bi bi-file-earmark-pdf"></i> <span>Export PDF</span></a>
    <a href="admin_logout.php"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></a>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="page-header mb-4 text-center">
        <h2>STUDENT ONLINE VOTING SYSTEM ADMIN DASHBOARD</h2>
        <hr>
    </div>

    <!-- Cards -->
    <div class="dashboard-cards">
        <div class="card-box">
            <h5>Total Candidates</h5>
            <span><?php $c=$conn->query("SELECT COUNT(*) as c FROM candidates")->fetch_assoc(); echo $c['c']; ?></span>
        </div>
        <div class="card-box">
            <h5>Total Voters</h5>
            <span><?php $c=$conn->query("SELECT COUNT(*) as c FROM voters")->fetch_assoc(); echo $c['c']; ?></span>
        </div>
        <div class="card-box">
            <h5>Votes Cast</h5>
            <span><?php $c=$conn->query("SELECT SUM(vote_count) as c FROM candidates")->fetch_assoc(); echo $c['c'] ?? 0; ?></span>
        </div>
    </div>

    <!-- Results Section -->
    <div class="results-section">
        <h3>Election Results</h3>
        <div class="mb-3 text-end">
            <label class="me-2 fw-bold">Filter by Branch:</label>
            <select id="branchFilter" class="form-select d-inline-block w-auto">
                <option value="all">All Branches</option>
                <?php foreach(array_keys($branches) as $branch): ?>
                    <option value="<?= $branch ?>"><?= $branch ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <table class="table table-striped text-center" id="resultsTable">
            <thead><tr><th>Branch</th><th>Candidate</th><th>Votes</th></tr></thead>
            <tbody>
                <?php foreach($data as $row): ?>
                <tr data-branch="<?= $row['department'] ?>">
                    <td><?= $row['department'] ?></td>
                    <td><?= $row['candidate_name'] ?></td>
                    <td><?= $row['vote_count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="chart-container">
        <canvas id="voteChart"></canvas>
    </div>
    <a href="export_results_pdf.php" class="btn btn-success">Print Results</a>
</div>

<script>
const rawData = <?= json_encode($data); ?>;

// Generate random colors
function generateColors(count) {
    let colors = [];
    for (let i = 0; i < count; i++) {
        colors.push(`rgba(${Math.floor(Math.random()*180)},${Math.floor(Math.random()*180)},${Math.floor(Math.random()*180)},0.8)`);
    }
    return colors;
}

// Chart
const ctx = document.getElementById('voteChart').getContext('2d');
let voteChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: rawData.map(r => r.candidate_name + " (" + r.department + ")"),
        datasets: [{
            label: 'Votes',
            data: rawData.map(r => r.vote_count),
            backgroundColor: generateColors(rawData.length),
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Branch Filter
document.getElementById("branchFilter").addEventListener("change", function() {
    const selected = this.value;
    let filtered = (selected==="all") ? rawData : rawData.filter(r => r.department===selected);

    voteChart.data.labels = filtered.map(r => r.candidate_name + " (" + r.department + ")");
    voteChart.data.datasets[0].data = filtered.map(r => r.vote_count);
    voteChart.data.datasets[0].backgroundColor = generateColors(filtered.length);
    voteChart.update();

    document.querySelectorAll("#resultsTable tbody tr").forEach(row => {
        row.style.display = (selected==="all" || row.getAttribute("data-branch")===selected) ? "" : "none";
    });
});

// Sidebar Toggle
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const mainContent = document.getElementById("mainContent");
menuBtn.addEventListener("click", () => {
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("expanded");
});
</script>
</body>
</html>
