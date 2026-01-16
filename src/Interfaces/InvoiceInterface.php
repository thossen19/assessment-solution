<?php

namespace InvoiceSystem\Interfaces;

/**
 * Interface for Invoice entities
 * 
 * Defines the contract that all invoice implementations must follow.
 * This ensures consistency across different invoice types and implementations.
 * 
 * @package InvoiceSystem\Interfaces
 * @author  Invoice System Team
 * @version 1.0.0
 * @since   1.0.0
 */
interface InvoiceInterface
{
    /**
     * Get the unique identifier for this invoice
     * 
     * @return string The invoice ID (e.g., "INV-2026-0001")
     */
    public function getId(): string;

    /**
     * Get the customer name associated with this invoice
     * 
     * @return string Customer name
     */
    public function getCustomer(): string;

    /**
     * Get all items included in this invoice
     * 
     * @return array Array of invoice items with name, price, quantity, and sale status
     */
    public function getItems(): array;

    /**
     * Calculate the subtotal (sum of all items before discounts)
     * 
     * @return float Subtotal amount
     */
    public function getSubtotal(): float;

    /**
     * Calculate the total amount after applying discounts
     * 
     * @return float Total amount
     */
    public function getTotal(): float;

    /**
     * Add an item to the invoice
     * 
     * @param string $name        Item name/description
     * @param float  $price       Unit price (must be non-negative)
     * @param int    $quantity    Quantity (must be positive)
     * @param bool   $isSaleItem  Whether this item is on sale (affects business rules)
     * 
     * @return void
     * @throws \InvalidArgumentException When validation fails
     */
    public function addItem(string $name, float $price, int $quantity, bool $isSaleItem = false): void;

    /**
     * Apply a percentage discount to the invoice
     * 
     * @param float $percent Discount percentage (0-100)
     * 
     * @return void
     * @throws \InvalidArgumentException When percentage is invalid
     */
    public function applyDiscount(float $percent): void;

    /**
     * Check if this invoice contains any sale items
     * 
     * @return bool True if at least one item is marked as a sale item
     */
    public function hasSaleItems(): bool;

    /**
     * Convert invoice to array representation for JSON serialization
     * 
     * @return array Invoice data as associative array
     */
    public function toArray(): array;

    /**
     * Save invoice to file storage
     * 
     * @param string $filename Target file path (optional, defaults to standard location)
     * 
     * @return bool True on successful save
     * @throws \Exception When save operation fails
     */
    public function saveToFile(string $filename = 'data/invoices.json'): bool;

    /**
     * Load invoice from file storage by ID
     * 
     * @param string $id        Invoice ID to load
     * @param string $filename  Source file path (optional, defaults to standard location)
     * 
     * @return static Loaded invoice instance
     * @throws \Exception When invoice not found or load fails
     */
    public static function loadFromFile(string $id, string $filename = 'data/invoices.json'): InvoiceInterface;
}
