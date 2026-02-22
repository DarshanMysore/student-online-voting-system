<?php
session_start();
include '../config.php';          // Database connection
require('../fpdf/fpdf.php');      // Include FPDF

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Extend FPDF for header and footer
class PDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('../logo.png', 10, 6, 30); // Adjust path as needed

        // College Name
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0, 0, 128);
        $this->Cell(40); // Move right for logo
        $this->Cell(0, 10, 'Maharaja Institute of Technology Mysore', 0, 1, 'C');

        // Report title
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(40);
        $this->Cell(0, 10, 'Student online Voting Results', 0, 1, 'C');

        // Extra space below header to prevent table overlap
        $this->Ln(15);

        // Table header
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(60, 10, 'Department', 1, 0, 'C', true);
        $this->Cell(80, 10, 'Candidate Name', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Votes', 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

// Create PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$fill = false;          // Alternating row colors
$totalVotes = 0;        // Total votes counter
$winners = [];          // Winners per department
$highestVotes = [];     // Max votes per department

// Fetch voting results
$sql = "SELECT department, candidate_name, vote_count FROM candidates ORDER BY department, vote_count DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $dept = $row['department'];
    $name = $row['candidate_name'];
    $votes = $row['vote_count'];

    // Track winners per department
    if (!isset($highestVotes[$dept]) || $votes > $highestVotes[$dept]) {
        $highestVotes[$dept] = $votes;
        $winners[$dept] = [$name];
    } elseif ($votes == $highestVotes[$dept]) {
        $winners[$dept][] = $name; // Tie
    }

    // Output table row
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(60, 10, $dept, 1, 0, 'C', $fill);
    $pdf->Cell(80, 10, $name, 1, 0, 'C', $fill);
    $pdf->Cell(40, 10, $votes, 1, 1, 'C', $fill);

    $fill = !$fill;
    $totalVotes += $votes;
}

// Total votes summary
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Total Votes: $totalVotes", 0, 1, 'R');

// Winners section
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Winners by Department:", 0, 1);
$pdf->SetFont('Arial', '', 12);

foreach ($winners as $dept => $names) {
    $pdf->Cell(0, 10, $dept . ": " . implode(", ", $names) . " (" . $highestVotes[$dept] . " votes)", 0, 1);
}

// Output PDF (force download)
$pdf->Output('D', 'Voting_Results_Report.pdf');
?>
