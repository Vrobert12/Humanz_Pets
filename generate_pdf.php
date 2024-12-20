<?php
require 'vendor/autoload.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrImage = $_POST['qrImage'];
    $sizes = [20, 50];
    $x = 10;
    $y = 20;
    $spacing = 10;
    $perRow = 3;
    $maxWidth = 210 - 2 * $x;
    $maxHeight = 297 - 2 * $y;

    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 16);

    $pdf->Cell(0, 10, "Qr Code image about user information of: " . $_SESSION['name'], 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);

    foreach ($sizes as $size) {
        for ($i = 0; $i < 5; $i++) {
            if ($x + $size > $maxWidth) {
                $x = 10;
                $y += $size + $spacing;
            }
            if ($y + $size > $maxHeight) {
                break;
            }
            $pdf->Image($qrImage, $x, $y, $size);
            $x += $size + $spacing;
        }
        $x = 10;
        $y += $size + $spacing;
    }

    $pdf->Output('D', $_SESSION['firstName']."_".$_SESSION['lastName'].'_qrcode.pdf');

    exit();
}
