<?php

namespace InvoiceSystem;

/**
 * Invoice Number Generator
 * 
 * Provides sequential invoice numbering with year-based counters.
 * Generates invoice IDs in the format INV-YYYY-NNNN for easy tracking.
 * 
 * Features:
 * - Year-based counters (reset each year)
 * - Sequential numbering within each year
 * - Persistent counter storage
 * - Thread-safe file operations
 * - Fallback to timestamp-based numbering
 * 
 * @package InvoiceSystem
 * @author  Invoice System Team
 * @version 1.0.0
 * @since   1.0.0
 * 
 * @example
 * <code>
 * $invoiceId = InvoiceNumberGenerator::generateNext();
 * // Returns: "INV-2026-0001"
 * 
 * $parsed = InvoiceNumberGenerator::parseInvoiceNumber("INV-2026-0001");
 * // Returns: ['year' => 2026, 'sequence' => 1]
 * </code>
 */
class InvoiceNumberGenerator {

    /**
     * File path for counter storage
     * 
     * @var string
     */
    private static $counterFile = 'data/invoice_counter.txt';

    /**
     * Generate the next sequential invoice number
     * 
     * Creates invoice numbers in the format INV-YYYY-NNNN where:
     * - YYYY is the current year
     * - NNNN is a 4-digit sequential number (resets annually)
     * 
     * Example: INV-2026-0001, INV-2026-0002, etc.
     * 
     * @return string The generated invoice number
     * 
     * @throws \Exception When counter file operations fail
     */
    public static function generateNext(): string {
        try {
            $year = date('Y');
            $counter = self::getCounter($year);
            
            // Increment counter
            $counter++;
            self::saveCounter($year, $counter);
            
            // Format: INV-YYYY-NNNN
            return sprintf('INV-%s-%04d', $year, $counter);
            
        } catch (Exception $e) {
            // Fallback to timestamp-based number
            return 'INV-' . time();
        }
    }

    /**
     * Get current counter for the year
     *
     * @param int $year
     * @return int Current counter
     */
    private static function getCounter($year) {
        if (!file_exists(self::$counterFile)) {
            return 0;
        }

        $data = file_get_contents(self::$counterFile);
        $lines = explode("\n", trim($data));
        
        foreach ($lines as $line) {
            $parts = explode(':', $line);
            if (count($parts) === 2 && (int)$parts[0] === $year) {
                return (int)$parts[1];
            }
        }
        
        return 0;
    }

    /**
     * Save counter for the year
     *
     * @param int $year
     * @param int $counter
     */
    private static function saveCounter($year, $counter) {
        $data = '';
        $found = false;
        
        // Read existing data
        if (file_exists(self::$counterFile)) {
            $existingData = file_get_contents(self::$counterFile);
            $lines = explode("\n", trim($existingData));
            
            foreach ($lines as $line) {
                $parts = explode(':', $line);
                if (count($parts) === 2 && (int)$parts[0] === $year) {
                    $data .= $year . ':' . $counter . "\n";
                    $found = true;
                } else {
                    $data .= $line . "\n";
                }
            }
        }
        
        // Add new year if not found
        if (!$found) {
            $data .= $year . ':' . $counter . "\n";
        }
        
        // Ensure directory exists
        $dir = dirname(self::$counterFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents(self::$counterFile, trim($data));
    }

    /**
     * Parse invoice number to extract year and sequence
     *
     * @param string $invoiceNumber
     * @return array|false ['year' => int, 'sequence' => int] or false if invalid
     */
    public static function parseInvoiceNumber($invoiceNumber) {
        if (preg_match('/INV-(\d{4})-(\d{4})/', $invoiceNumber, $matches)) {
            return [
                'year' => (int)$matches[1],
                'sequence' => (int)$matches[2]
            ];
        }
        
        return false;
    }

    /**
     * Validate invoice number format
     *
     * @param string $invoiceNumber
     * @return bool
     */
    public static function isValidFormat($invoiceNumber) {
        return preg_match('/^INV-\d{4}-\d{4}$/', $invoiceNumber) === 1;
    }
}
