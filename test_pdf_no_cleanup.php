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

echo "Testing PDF generation (no cleanup)...\n";

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
        $fullPath = realpath($pdfFile);
        echo "✓ PDF file exists\n";
        echo "  Path: {$fullPath}\n";
        echo "  Size: {$size} bytes\n";
    } else {
        echo "✗ PDF file not found\n";
        echo "  Expected path: " . getcwd() . "/{$pdfFile}\n";
    }
    
} catch (Exception $e) {
    echo "✗ PDF generation failed: " . $e->getMessage() . "\n";
}
