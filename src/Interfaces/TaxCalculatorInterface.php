<?php

namespace InvoiceSystem\Interfaces;

/**
 * Interface for tax calculation services
 * 
 * Defines the contract for tax calculation implementations.
 * Supports different tax calculation methods and rate sources.
 * 
 * @package InvoiceSystem\Interfaces
 * @author  Invoice System Team
 * @version 1.0.0
 * @since   1.0.0
 */
interface TaxCalculatorInterface
{
    /**
     * Calculate tax amount for a given subtotal and region
     * 
     * @param float  $subtotal The amount before tax
     * @param string $region   Region code (e.g., "US-CA", "CA-ON", "EU")
     * 
     * @return float Calculated tax amount
     * @throws \Exception When tax calculation fails
     */
    public static function calculateTax(float $subtotal, string $region = 'US-CA'): float;

    /**
     * Get the tax rate for a specific region
     * 
     * @param string $region Region code
     * 
     * @return float Tax rate as decimal (e.g., 0.0725 for 7.25%)
     * @throws \Exception When region is not found
     */
    public static function getTaxRate(string $region): float;

    /**
     * Check if a region is supported
     * 
     * @param string $region Region code to check
     * 
     * @return bool True if region is supported
     */
    public static function isRegionSupported(string $region): bool;

    /**
     * Get all supported regions
     * 
     * @return array Array of supported region codes
     */
    public static function getSupportedRegions(): array;
}
