<?php

namespace InvoiceSystem;

use InvoiceSystem\Interfaces\InvoiceInterface;
use InvoiceSystem\InvoiceNumberGenerator;
use InvoiceSystem\ErrorHandler;

/**
 * Invoice Entity
 * 
 * Represents a commercial invoice with items, discounts, and calculations.
 * Implements the InvoiceInterface to ensure consistent behavior across the system.
 * 
 * Features:
 * - Sequential invoice numbering (INV-YYYY-NNNN format)
 * - Item management with validation
 * - Discount application (manual and business rules)
 * - Sale item tracking for business logic
 * - JSON serialization and file persistence
 * - Comprehensive input validation
 * 
 * @package InvoiceSystem
 * @author  Invoice System Team
 * @version 1.0.0
 * @since   1.0.0
 * 
 * @example
 * <code>
 * $invoice = new Invoice("Acme Corp");
 * $invoice->addItem("Product A", 100.00, 2);
 * $invoice->addItem("Product B", 50.00, 1, true); // Sale item
 * $invoice->applyDiscount(10); // 10% discount
 * echo $invoice->getTotal(); // Returns discounted total
 * </code>
 */
class Invoice implements InvoiceInterface
{

    /**
     * Customer name
     * 
     * @var string
     */
    private $customer;
    
    /**
     * Array of invoice items
     * 
     * Each item structure:
     * - name: string (item name/description)
     * - price: float (unit price)
     * - quantity: int (quantity)
     * - is_sale_item: bool (sale status for business rules)
     * 
     * @var array
     */
    private $items = [];
    
    /**
     * Discount amount in currency
     * 
     * @var float
     */
    private $discount = 0;
    
    /**
     * Unique invoice identifier
     * 
     * Format: INV-YYYY-NNNN (e.g., INV-2026-0001)
     * 
     * @var string
     */
    private $id;
    
    /**
     * Invoice creation timestamp
     * 
     * Format: Y-m-d H:i:s (e.g., 2026-01-16 21:30:00)
     * 
     * @var string
     */
    private $createdAt;

    /**
     * Create a new invoice
     * 
     * Automatically generates a sequential invoice ID and sets creation timestamp.
     * Validates customer name to ensure data integrity.
     * 
     * @param string $customerName Name of the customer (must be non-empty string)
     * 
     * @throws \InvalidArgumentException When customer name is invalid
     */
    public function __construct(string $customerName) {
        if (empty($customerName) || !is_string($customerName)) {
            throw new \InvalidArgumentException('Customer name must be a non-empty string');
        }
        $this->customer = $customerName;
        $this->id = InvoiceNumberGenerator::generateNext();
        $this->createdAt = date('Y-m-d H:i:s');
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(string $name, float $price, int $quantity, bool $isSaleItem = false): void {
        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Item name must be a non-empty string');
        }
        if (!is_numeric($price) || $price < 0) {
            throw new \InvalidArgumentException('Item price must be a non-negative number');
        }
        if (!is_numeric($quantity) || $quantity <= 0) {
            throw new \InvalidArgumentException('Item quantity must be a positive number');
        }
        
        $this->items[] = [
            'name' => $name,
            'price' => (float) $price,
            'quantity' => (int) $quantity,
            'is_sale_item' => (bool) $isSaleItem
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal(): float {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total - $this->discount;
    }

    /**
     * {@inheritdoc}
     */
    public function applyDiscount(float $percent): void {
        if (!is_numeric($percent) || $percent < 0 || $percent > 100) {
            throw new \InvalidArgumentException('Discount percentage must be between 0 and 100');
        }
        
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $this->discount = $subtotal * ($percent / 100);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer(): string {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'customer' => $this->customer,
            'items' => $this->items,
            'discount' => $this->discount,
            'total' => $this->getTotal(),
            'created_at' => $this->createdAt
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saveToFile(string $filename = 'data/invoices.json'): bool {
        try {
            $data = $this->toArray();
            $invoices = [];

            // Load existing invoices if file exists
            if (file_exists($filename)) {
                $contents = file_get_contents($filename);
                $existingInvoices = json_decode($contents, true);
                
                if ($existingInvoices === null) {
                    ErrorHandler::logError("Invalid JSON in invoices file, starting fresh", 'WARNING');
                    $existingInvoices = [];
                }
                
                // Handle both single invoice and array of invoices
                if (isset($existingInvoices['id'])) {
                    $invoices = [$existingInvoices];
                } else {
                    $invoices = $existingInvoices;
                }
            }

            // Add new invoice
            $invoices[] = $data;

            // Save all invoices back to file
            $jsonData = json_encode($invoices, JSON_PRETTY_PRINT);
            if ($jsonData === false) {
                throw new Exception("Failed to encode invoice data to JSON");
            }
            
            $result = file_put_contents($filename, $jsonData, LOCK_EX);
            if ($result === false) {
                throw new Exception("Failed to write to file: {$filename}");
            }
            
            return true;
            
        } catch (Exception $e) {
            ErrorHandler::handleFileError('save', $filename, $e);
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasSaleItems(): bool {
        foreach ($this->items as $item) {
            if (isset($item['is_sale_item']) && $item['is_sale_item']) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotal(): float {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return $subtotal;
    }

    /**
     * {@inheritdoc}
     */
    public static function loadFromFile(string $id, string $filename = 'data/invoices.json'): InvoiceInterface {
        try {
            if (!file_exists($filename)) {
                throw new Exception("Invoice file not found: {$filename}");
            }

            $contents = file_get_contents($filename);
            if ($contents === false) {
                throw new Exception("Failed to read invoice file: {$filename}");
            }
            
            $invoices = json_decode($contents, true);
            if ($invoices === null) {
                ErrorHandler::handleDataError('load', $filename, new Exception("Invalid JSON in invoice file"));
                throw new Exception("Invalid JSON in invoice file: {$filename}");
            }

            // Handle both single invoice and array of invoices
            if (isset($invoices['id'])) {
                $invoices = [$invoices];
            }

            foreach ($invoices as $invoiceData) {
                if ($invoiceData['id'] == $id) {
                    $invoice = new Invoice($invoiceData['customer']);
                    $invoice->id = $invoiceData['id'];
                    $invoice->discount = $invoiceData['discount'];

                    foreach ($invoiceData['items'] as $item) {
                        $quantity = isset($item['quantity']) ? $item['quantity'] : $item['qty'];
                        $isSaleItem = isset($item['is_sale_item']) ? $item['is_sale_item'] : false;
                        $invoice->addItem($item['name'], $item['price'], $quantity, $isSaleItem);
                    }

                    return $invoice;
                }
            }

            throw new Exception("Invoice not found: " . $id);
            
        } catch (Exception $e) {
            ErrorHandler::handleFileError('load', $filename, $e);
            throw $e;
        }
    }
}
