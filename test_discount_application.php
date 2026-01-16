<?php

require_once __DIR__ . '/src/Interfaces/InvoiceInterface.php';
require_once __DIR__ . '/src/Interfaces/PDFGeneratorInterface.php';
require_once __DIR__ . '/src/Interfaces/TaxCalculatorInterface.php';
require_once __DIR__ . '/src/Invoice.php';
require_once __DIR__ . '/src/InvoiceNumberGenerator.php';
require_once __DIR__ . '/src/ErrorHandler.php';
require_once __DIR__ . '/src/InvoiceCalculator.php';

use InvoiceSystem\Invoice;
use InvoiceSystem\InvoiceCalculator;

echo "Testing discount application...\n";

// Test 1: Manual discount application
echo "\n1. Testing manual discount:\n";
$invoice1 = new Invoice("Customer 1");
$invoice1->addItem("Product A", 100.00, 2);
$invoice1->addItem("Product B", 50.00, 1);

$subtotal1 = $invoice1->getSubtotal();
echo "   Subtotal: $" . number_format($subtotal1, 2) . "\n";

$invoice1->applyDiscount(10);
$total1 = $invoice1->getTotal();
echo "   After 10% discount: $" . number_format($total1, 2) . "\n";

$expected1 = $subtotal1 * 0.9; // 10% discount
if (abs($total1 - $expected1) < 0.01) {
    echo "   ✓ Manual discount working correctly\n";
} else {
    echo "   ✗ Manual discount failed\n";
}

// Test 2: Business rules (automatic discount)
echo "\n2. Testing business rules:\n";
$invoice2 = new Invoice("Customer 2");
$invoice2->addItem("Product A", 600.00, 2); // $1200 total, should get 5% discount

$subtotal2 = $invoice2->getSubtotal();
echo "   Subtotal: $" . number_format($subtotal2, 2) . "\n";

InvoiceCalculator::applyBusinessRules($invoice2);
$total2 = $invoice2->getTotal();
echo "   After business rules: $" . number_format($total2, 2) . "\n";

$expected2 = $subtotal2 * 0.95; // 5% discount
if (abs($total2 - $expected2) < 0.01) {
    echo "   ✓ Business rules discount working correctly\n";
} else {
    echo "   ✗ Business rules discount failed\n";
}

// Test 3: Business rules with sale items (no discount)
echo "\n3. Testing business rules with sale items:\n";
$invoice3 = new Invoice("Customer 3");
$invoice3->addItem("Product A", 600.00, 2); // $1200 total
$invoice3->addItem("Sale Item", 100.00, 1, true); // Sale item

$subtotal3 = $invoice3->getSubtotal();
echo "   Subtotal: $" . number_format($subtotal3, 2) . "\n";
echo "   Has sale items: " . ($invoice3->hasSaleItems() ? 'Yes' : 'No') . "\n";

InvoiceCalculator::applyBusinessRules($invoice3);
$total3 = $invoice3->getTotal();
echo "   After business rules: $" . number_format($total3, 2) . "\n";

if (abs($total3 - $subtotal3) < 0.01) {
    echo "   ✓ No discount applied (correct due to sale items)\n";
} else {
    echo "   ✗ Incorrect discount applied\n";
}

echo "\n✓ All discount tests completed\n";
