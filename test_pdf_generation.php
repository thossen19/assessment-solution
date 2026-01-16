<?php

require_once __DIR__ . '/src/Interfaces/InvoiceInterface.php';
require_once __DIR__ . '/src/Interfaces/PDFGeneratorInterface.php';
require_once __DIR__ . '/src/Interfaces/TaxCalculatorInterface.php';
require_once __DIR__ . '/src/Invoice.php';
require_once __DIR__ . '/src/InvoiceNumberGenerator.php';
require_once __DIR__ . '/src/ErrorHandler.php';
require_once __DIR__ . '/src/PDFGenerator.php';

use InvoiceSystem\Invoice;
use InvoiceSystem\PDFGenerator;

echo "Testing PDF generation...\n";

try {
    // Create test invoice
    $invoice = new Invoice("Test Customer");
    $invoice->addItem("Product A", 100.00, 2);
    $invoice->addItem("Product B", 50.00, 1);
    
    echo "✓ Invoice created\n";
    
    // Generate PDF
    $pdfGenerator = new PDFGenerator();
    $pdfFile = $pdfGenerator->generatePDF($invoice);
    
    echo "✓ PDF generated: {$pdfFile}\n";
    
    // Check if file exists
    if (file_exists($pdfFile)) {
        $size = filesize($pdfFile);
        echo "✓ PDF file exists, size: {$size} bytes\n";
    } else {
        echo "✗ PDF file not found\n";
    }
    
    // Clean up
    if (file_exists($pdfFile)) {
        unlink($pdfFile);
        echo "✓ Test PDF cleaned up\n";
    }
    
} catch (Exception $e) {
    echo "✗ PDF generation failed: " . $e->getMessage() . "\n";
}
