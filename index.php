<?php
require_once('vendor/autoload.php');
require_once('phpqrcode/qrlib.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

// Define the path to the directory where QR codes will be saved
$qrCodesDir = 'QRCodes/';

// Check if the directory exists, if not, create it
if (!file_exists($qrCodesDir)) {
    mkdir($qrCodesDir, 0777, true);
}

// Load the Excel spreadsheet
$spreadsheet = IOFactory::load('Systems.xlsx');

// Get the first worksheet
$worksheet = $spreadsheet->getActiveSheet();

// Loop through each row in the worksheet
foreach ($worksheet->getRowIterator() as $row) {
    // Get the data from the row
    $system = [];
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells, even if it is not set
    foreach ($cellIterator as $cell) {
        $system[] = $cell->getValue();
    }

    // Concatenate all system info into a string
    $systemInfo = "System ID: {$system[0]}\nCPU: {$system[1]}\nMemory: {$system[2]}\nDisk 0: {$system[3]}\nDisk 1: {$system[4]}\nGPU 0: {$system[5]}\nGPU 1: {$system[6]}\nDisplay: {$system[7]}\nKeyboard Brand: {$system[8]}\nKeyboard ID: {$system[9]}\nMouse Brand: {$system[10]}\nMouse ID: {$system[11]}";

    // Define the filename for the QR code image
    $filename = $qrCodesDir . $system[0] . '.png';

    // Generate the QR code and save it to the file
    QRcode::png($systemInfo, $filename, QR_ECLEVEL_L, 10);

    // Print the System ID below the QR code (requires GD library)
    $image = imagecreatefrompng($filename);
    $black = imagecolorallocate($image, 0, 0, 0);
    $fontPath = 'arial.ttf'; // Replace with the path to your TTF font
    $fontSize = 10; // Adjust the font size as needed
    $textBox = imagettfbbox($fontSize, 0, $fontPath, $system[0]);
    $textWidth = $textBox[2] - $textBox[0];
    $x = (imagesx($image) - $textWidth) / 2;
    $y = imagesy($image) - 10; // Adjust the Y-coordinate as needed
    imagettftext($image, $fontSize, 0, $x, $y, $black, $fontPath, $system[0]);
    imagepng($image, $filename);
    imagedestroy($image);
}

echo "QR codes generated successfully!";
