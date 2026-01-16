# Invoice Management System

A professional PHP invoice management system with PDF generation, tax calculations, and comprehensive business logic.

## Features

### Core Functionality
- **Invoice Creation**: Create professional invoices with customer information and line items
- **Sequential Numbering**: Automatic invoice numbering in format `INV-YYYY-NNNN`
- **Item Management**: Add items with validation for prices and quantities
- **Discount System**: Manual percentage discounts and automatic business rule discounts
- **Sale Item Tracking**: Mark items as sale items to affect business rules

### Advanced Features
- **PDF Generation**: Professional PDF invoices with CSS styling (requires Composer dependencies)
- **Dynamic Tax Rates**: Region-specific tax rates loaded from JSON configuration
- **Business Rules**: Automatic 5% discount for orders over $1000 (excluding sale items)
- **Data Persistence**: JSON-based storage with proper file handling
- **Comprehensive Validation**: Input validation for all data fields
- **Error Handling**: Centralized error logging and management

### Technical Features
- **Object-Oriented Architecture**: Clean, maintainable code structure
- **Interface-Based Design**: Contracts for extensibility and testing
- **Namespace Organization**: Proper PSR-4 autoloading support
- **Comprehensive Documentation**: Full PHPDoc documentation
- **Unit Tests**: Test suite for core functionality

## Installation

### Prerequisites
- PHP 7.4 or higher
- Composer (for PDF generation)

### Setup

1. **Clone or download the system files**
2. **Install dependencies** (for PDF generation):
   ```bash
   composer install
   ```

3. **Ensure data directory is writable**:
   ```bash
   chmod 755 data/
   ```

## Quick Start

### Basic Usage

```php
<?php
require_once 'src/Invoice.php';
require_once 'src/InvoiceCalculator.php';

use InvoiceSystem\Invoice;
use InvoiceSystem\InvoiceCalculator;

// Create a new invoice
$invoice = new Invoice("Acme Corporation");

// Add items
$invoice->addItem("Product A", 100.00, 2);
$invoice->addItem("Product B", 50.00, 1);
$invoice->addItem("Sale Item", 75.00, 1, true); // Mark as sale item

// Apply manual discount
$invoice->applyDiscount(10); // 10% discount

// Apply business rules (automatic discount for orders > $1000)
InvoiceCalculator::applyBusinessRules($invoice);

// Get totals
echo "Subtotal: $" . number_format($invoice->getSubtotal(), 2) . "\n";
echo "Total: $" . number_format($invoice->getTotal(), 2) . "\n";

// Save to file
$invoice->saveToFile();
```

### PDF Generation

```php
<?php
require_once 'src/PDFGenerator.php';

use InvoiceSystem\PDFGenerator;

try {
    $pdfGenerator = new PDFGenerator();
    $pdfFile = $pdfGenerator->generatePDF($invoice);
    echo "PDF generated: {$pdfFile}\n";
} catch (Exception $e) {
    echo "PDF generation not available: " . $e->getMessage() . "\n";
}
```

### Tax Calculation

```php
<?php
use InvoiceSystem\InvoiceCalculator;

$subtotal = 100.00;
$tax = InvoiceCalculator::calculateTax($subtotal, 'US-CA');
echo "Tax: $" . number_format($tax, 2) . "\n";
```

## Architecture

### Directory Structure

```
invoice-system/
├── src/
│   ├── Interfaces/           # Contract definitions
│   │   ├── InvoiceInterface.php
│   │   ├── PDFGeneratorInterface.php
│   │   └── TaxCalculatorInterface.php
│   ├── Invoice.php           # Main invoice entity
│   ├── InvoiceCalculator.php # Business logic and calculations
│   ├── InvoiceNumberGenerator.php # Sequential numbering
│   ├── PDFGenerator.php     # PDF generation (Dompdf)
│   └── ErrorHandler.php     # Centralized error handling
├── data/
│   ├── invoices.json        # Invoice storage
│   ├── tax_rates.json       # Tax configuration
│   └── invoice_counter.txt  # Numbering counter
├── tests/
│   └── InvoiceTest.php      # Test suite
├── invoices/                # Generated PDFs
├── composer.json            # Dependencies
└── README.md               # This file
```

### Key Components

#### Invoice (Entity)
- Implements `InvoiceInterface`
- Handles invoice data and basic operations
- Provides validation and business logic integration

#### InvoiceCalculator (Service)
- Static utility methods for calculations
- Tax rate loading and application
- Business rule implementation

#### PDFGenerator (Service)
- Implements `PDFGeneratorInterface`
- HTML to PDF conversion using Dompdf
- Graceful fallback when dependencies unavailable

#### InvoiceNumberGenerator (Utility)
- Sequential invoice numbering
- Year-based counters with persistence
- Thread-safe file operations

## Configuration

### Tax Rates

Edit `data/tax_rates.json` to configure tax rates:

```json
{
  "US": {
    "CA": 0.0725,
    "NY": 0.08,
    "TX": 0.0625,
    "default": 0.06
  },
  "CA": {
    "ON": 0.13,
    "BC": 0.12,
    "default": 0.05
  }
}
```

### Business Rules

Current business rules implemented:
- Orders over $1000 get automatic 5% discount
- Discount does not apply to invoices containing sale items
- Discount is calculated before tax

## API Reference

### Invoice Class

#### Constructor
```php
public function __construct(string $customerName)
```

#### Methods
```php
public function addItem(string $name, float $price, int $quantity, bool $isSaleItem = false): void
public function applyDiscount(float $percent): void
public function getTotal(): float
public function getSubtotal(): float
public function hasSaleItems(): bool
public function saveToFile(string $filename = 'data/invoices.json'): bool
public static function loadFromFile(string $id, string $filename = 'data/invoices.json'): InvoiceInterface
```

### InvoiceCalculator Class

#### Static Methods
```php
public static function calculateTax(float $subtotal, string $region = 'US-CA'): float
public static function applyBusinessRules(InvoiceInterface $invoice): InvoiceInterface
public static function validateInvoice(InvoiceInterface $invoice): array
public static function formatCurrency(float $amount): string
```

## Testing

Run the test suite:

```bash
php run_tests.php
```

The test suite includes:
- Invoice creation and validation
- Total calculations
- File save/load operations
- Tax calculations
- Error handling

## Error Handling

The system includes comprehensive error handling:

- **Input Validation**: All user inputs are validated with clear error messages
- **File Operations**: Graceful handling of file read/write errors
- **PDF Generation**: Fallback when dependencies are unavailable
- **Tax Calculation**: Fallback to default rate when configuration fails
- **Centralized Logging**: All errors logged to `data/error_log.txt`

## Dependencies

### Required
- PHP 7.4+

### Optional (for PDF generation)
- **dompdf/dompdf**: ^2.0
- Composer autoloader

## Contributing

When contributing to this system:

1. Follow PSR-4 autoloading standards
2. Implement appropriate interfaces
3. Add comprehensive PHPDoc documentation
4. Include unit tests for new features
5. Update this README documentation

## License

This project is provided as-is for educational and development purposes.

## Support

For issues and questions:
1. Check the test suite for expected behavior
2. Review error logs in `data/error_log.txt`
3. Validate configuration files are properly formatted
4. Ensure file permissions are correct for data directory
