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

echo "Testing input validation...\n";

// Test 1: Invalid customer name
echo "\n1. Testing invalid customer name:\n";
try {
    $invoice1 = new Invoice("");
    echo "   ✗ Empty customer name should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Empty customer name rejected\n";
}

try {
    $invoice2 = new Invoice(123);
    echo "   ✗ Non-string customer name should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Non-string customer name rejected\n";
}

// Test 2: Invalid item data
echo "\n2. Testing invalid item data:\n";
$invoice3 = new Invoice("Valid Customer");

try {
    $invoice3->addItem("", 100.00, 1);
    echo "   ✗ Empty item name should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Empty item name rejected\n";
}

try {
    $invoice3->addItem("Valid Item", -10.00, 1);
    echo "   ✗ Negative price should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Negative price rejected\n";
}

try {
    $invoice3->addItem("Valid Item", 100.00, 0);
    echo "   ✗ Zero quantity should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Zero quantity rejected\n";
}

try {
    $invoice3->addItem("Valid Item", 100.00, -1);
    echo "   ✗ Negative quantity should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Negative quantity rejected\n";
}

// Test 3: Invoice validation
echo "\n3. Testing invoice validation:\n";
$validInvoice = new Invoice("Valid Customer");
$validInvoice->addItem("Valid Item", 100.00, 2);

$errors = InvoiceCalculator::validateInvoice($validInvoice);
if (empty($errors)) {
    echo "   ✓ Valid invoice passes validation\n";
} else {
    echo "   ✗ Valid invoice failed validation: " . implode(', ', $errors) . "\n";
}

// Test 4: Invalid invoice validation
echo "\n4. Testing invalid invoice validation:\n";
$invalidInvoice = new Invoice("Valid Customer");
// Add no items

$errors = InvoiceCalculator::validateInvoice($invalidInvoice);
if (!empty($errors)) {
    echo "   ✓ Invalid invoice fails validation: " . implode(', ', $errors) . "\n";
} else {
    echo "   ✗ Invalid invoice should fail validation\n";
}

// Test 5: Discount validation
echo "\n5. Testing discount validation:\n";
$invoice5 = new Invoice("Valid Customer");
$invoice5->addItem("Valid Item", 100.00, 1);

try {
    $invoice5->applyDiscount(-5);
    echo "   ✗ Negative discount should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Negative discount rejected\n";
}

try {
    $invoice5->applyDiscount(150);
    echo "   ✗ Discount > 100% should fail\n";
} catch (InvalidArgumentException $e) {
    echo "   ✓ Discount > 100% rejected\n";
}

echo "\n✓ All input validation tests completed\n";
