<?php
// ========================================================================
// FILE: generate_report.php
// DILENGKAPI DENGAN LOGIKA UNTUK LAPORAN WORKOUT
// ========================================================================
?>
<?php
require_once 'classes/User.php';
if (!User::isLoggedIn()) { die("Akses ditolak."); }

require_once 'config/database.php';
require_once 'lib/fpdf/fpdf.php';

$report_type = $_POST['report_type'] ?? 'all_members';

class PDF extends FPDF {
    public $reportTitle = 'Laporan';

    function Header() {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10, $this->reportTitle, 0, 1,'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function MemberTable($header, $data) {
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('','B', 9);
        $w = array(25, 45, 50, 25, 25, 20); // Widths of columns
        for($i=0; $i<count($header); $i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        
        $this->SetFont('','', 8);
        foreach($data as $row) {
            $this->Cell($w[0],6,$row['id'],'LR',0,'L');
            $this->Cell($w[1],6, substr($row['name'], 0, 25),'LR',0,'L');
            $this->Cell($w[2],6, substr($row['email'], 0, 30),'LR',0,'L');
            $this->Cell($w[3],6,$row['membership'],'LR',0,'C');
            $this->Cell($w[4],6,$row['join_date'],'LR',0,'C');
            $this->Cell($w[5],6,$row['status'],'LR',0,'C');
            $this->Ln();
        }
        $this->Cell(array_sum($w),0,'','T');
    }
    
    function WorkoutTable($header, $data) {
        $this->SetFillColor(200, 220, 255);
        $this->SetTextColor(0);
        $this->SetFont('','B', 9);
        $w = array(60, 40, 30, 35, 25); // Widths of columns
        for($i=0; $i<count($header); $i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        
        $this->SetFont('','', 8);
        foreach($data as $row) {
            $this->Cell($w[0],6, substr($row['name'], 0, 35),'LR',0,'L');
            $this->Cell($w[1],6,$row['category'],'LR',0,'L');
            $this->Cell($w[2],6,$row['duration'] . ' min','LR',0,'C');
            $this->Cell($w[3],6,$row['difficulty'],'LR',0,'C');
            $this->Cell($w[4],6,$row['status'],'LR',0,'C');
            $this->Ln();
        }
        $this->Cell(array_sum($w),0,'','T');
    }
}

$database = new Database();
$db = $database->getConnection();
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P', 'A4');

if (strpos($report_type, 'member') !== false) {
    require_once 'classes/Member.php';
    $member = new Member($db);
    $stmt = $member->read();
    $data = [];
    while ($row = $stmt->fetch_assoc()) {
        if ($report_type == 'active_members' && $row['status'] != 'active') {
            continue;
        }
        $data[] = $row;
    }
    
    $pdf->reportTitle = 'Laporan Data Member';
    // Panggil ulang header untuk update judul
    $pdf->Header(); 
    $header = array('ID', 'Nama', 'Email', 'Membership', 'Join Date', 'Status');
    $pdf->MemberTable($header, $data);

} elseif ($report_type == 'all_workouts') {
    require_once 'classes/Workout.php';
    $workout = new Workout($db);
    $stmt = $workout->read();
    $data = $stmt->fetch_all(MYSQLI_ASSOC);
    
    $pdf->reportTitle = 'Laporan Rencana Workout';
    $pdf->Header();
    $header = array('Nama Rencana', 'Kategori', 'Durasi', 'Tingkat Kesulitan', 'Status');
    $pdf->WorkoutTable($header, $data);
}

$pdf->Output('D', 'Laporan_FitTracker.pdf');
exit;
?>
