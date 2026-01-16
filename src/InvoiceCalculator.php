<?php

namespace InvoiceSystem;

use InvoiceSystem\Interfaces\TaxCalculatorInterface;
use InvoiceSystem\Interfaces\InvoiceInterface;

/**
 * InvoiceCalculator - Helper class for invoice calculations
 *
 * Static utility methods for business logic
 * Client keeps changing their mind on requirements...
 */
class InvoiceCalculator implements TaxCalculatorInterface {

    /**
     * {@inheritdoc}
     */
    public static function calculateTax(float $subtotal, string $region = 'US-CA'): float {
        try {
            $taxFile = __DIR__ . '/../data/tax_rates.json';
            if (!file_exists($taxFile)) {
                throw new Exception("Tax rates file not found: {$taxFile}");
            }

            $taxData = json_decode(file_get_contents($taxFile), true);
            if ($taxData === null) {
                throw new Exception("Invalid tax rates JSON in file: {$taxFile}");
            }

            // Parse region (format: COUNTRY-STATE or just COUNTRY)
            $parts = explode('-', $region);
            $country = strtoupper($parts[0]);
            $state = isset($parts[1]) ? strtoupper($parts[1]) : null;

            // Look up tax rate
            $taxRate = 0;

            if (isset($taxData[$country])) {
                if ($state && isset($taxData[$country][$state])) {
                    $taxRate = $taxData[$country][$state];
                } elseif (isset($taxData[$country]['default'])) {
                    $taxRate = $taxData[$country]['default'];
                }
            }

            // If no specific rate found, try to find a general default
            if ($taxRate === 0) {
                foreach ($taxData as $countryData) {
                    if (isset($countryData['default'])) {
                        $taxRate = $countryData['default'];
                        break;
                    }
                }
            }

            return $subtotal * $taxRate;

        } catch (Exception $e) {
            // Fallback to 10% if there's an error
            error_log("Tax calculation error: " . $e->getMessage());
            return $subtotal * 0.10;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getTaxRate(string $region): float {
        try {
            $taxFile = __DIR__ . '/../data/tax_rates.json';
            if (!file_exists($taxFile)) {
                throw new Exception("Tax rates file not found: {$taxFile}");
            }

            $taxData = json_decode(file_get_contents($taxFile), true);
            if ($taxData === null) {
                throw new Exception("Invalid tax rates JSON in file: {$taxFile}");
            }

            // Parse region (format: COUNTRY-STATE or just COUNTRY)
            $parts = explode('-', $region);
            $country = strtoupper($parts[0]);
            $state = isset($parts[1]) ? strtoupper($parts[1]) : null;

            // Look up tax rate
            $taxRate = 0;

            if (isset($taxData[$country])) {
                if ($state && isset($taxData[$country][$state])) {
                    $taxRate = $taxData[$country][$state];
                } elseif (isset($taxData[$country]['default'])) {
                    $taxRate = $taxData[$country]['default'];
                }
            }

            // If no specific rate found, try to find a general default
            if ($taxRate === 0) {
                foreach ($taxData as $countryData) {
                    if (isset($countryData['default'])) {
                        $taxRate = $countryData['default'];
                        break;
                    }
                }
            }

            if ($taxRate === 0) {
                throw new Exception("Tax rate not found for region: {$region}");
            }

            return $taxRate;

        } catch (Exception $e) {
            throw new Exception("Failed to get tax rate for region {$region}: " . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function isRegionSupported(string $region): bool {
        try {
            self::getTaxRate($region);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportedRegions(): array {
        try {
            $taxFile = __DIR__ . '/../data/tax_rates.json';
            if (!file_exists($taxFile)) {
                return [];
            }

            $taxData = json_decode(file_get_contents($taxFile), true);
            if ($taxData === null) {
                return [];
            }

            $regions = [];
            foreach ($taxData as $country => $states) {
                if (is_array($states)) {
                    foreach ($states as $state => $rate) {
                        if ($state !== 'default') {
                            $regions[] = strtoupper($country . '-' . $state);
                        }
                    }
                    $regions[] = strtoupper($country); // Country-level default
                }
            }

            return $regions;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Apply business rules to an invoice
     *
     * Business rules:
     * 1. Orders over $1000 (before tax) get automatic 5% discount
     * 2. Discount does NOT apply to invoices with sale items
     * 3. Discount is applied before tax calculation
     *
     * @param InvoiceInterface $invoice
     * @return InvoiceInterface Modified invoice
     */
    public static function applyBusinessRules(InvoiceInterface $invoice): InvoiceInterface {
        try {
            $subtotal = $invoice->getSubtotal();
            
            // Rule 1: Check if order is over $1000
            if ($subtotal > 1000) {
                // Rule 2: Only apply if no sale items present
                if (!$invoice->hasSaleItems()) {
                    // Rule 3: Apply 5% discount
                    $invoice->applyDiscount(5);
                }
            }
            
            return $invoice;
            
        } catch (Exception $e) {
            error_log("Business rules application error: " . $e->getMessage());
            return $invoice;
        }
    }

    /**
     * Calculate line item total
     * This one actually works correctly!
     *
     * @param array $item Item with price and quantity/qty
     * @return float Line item total
     */
    public static function calculateLineItem($item) {
        $price = $item['price'];

        // Handle both 'quantity' and 'qty' naming
        // (Someone was inconsistent with naming)
        $quantity = isset($item['quantity']) ? $item['quantity'] : $item['qty'];

        return $price * $quantity;
    }

    /**
     * Format currency for display
     * Quick helper I added
     *
     * @param float $amount
     * @return string Formatted currency
     */
    public static function formatCurrency($amount) {
        return '$' . number_format($amount, 2);
    }

    /**
     * Validate invoice data
     *
     * @param Invoice $invoice
     * @return array Array of validation errors (empty if valid)
     */
    public static function validateInvoice($invoice) {
        $errors = [];

        try {
            // Validate customer name
            $customer = $invoice->getCustomer();
            if (empty($customer) || !is_string($customer)) {
                $errors[] = 'Customer name is required and must be a string';
            } elseif (strlen($customer) > 255) {
                $errors[] = 'Customer name cannot exceed 255 characters';
            }

            // Validate items
            $items = $invoice->getItems();
            if (empty($items)) {
                $errors[] = 'Invoice must have at least one item';
            } else {
                foreach ($items as $index => $item) {
                    $itemNum = $index + 1;
                    
                    // Validate item name
                    if (empty($item['name']) || !is_string($item['name'])) {
                        $errors[] = "Item {$itemNum}: Name is required and must be a string";
                    } elseif (strlen($item['name']) > 255) {
                        $errors[] = "Item {$itemNum}: Name cannot exceed 255 characters";
                    }

                    // Validate price
                    if (!isset($item['price']) || !is_numeric($item['price'])) {
                        $errors[] = "Item {$itemNum}: Price is required and must be numeric";
                    } elseif ($item['price'] < 0) {
                        $errors[] = "Item {$itemNum}: Price cannot be negative";
                    } elseif ($item['price'] > 999999.99) {
                        $errors[] = "Item {$itemNum}: Price cannot exceed $999,999.99";
                    }

                    // Validate quantity
                    $quantity = isset($item['quantity']) ? $item['quantity'] : $item['qty'];
                    if (!isset($quantity) || !is_numeric($quantity)) {
                        $errors[] = "Item {$itemNum}: Quantity is required and must be numeric";
                    } elseif ($quantity <= 0) {
                        $errors[] = "Item {$itemNum}: Quantity must be positive";
                    } elseif ($quantity > 999999) {
                        $errors[] = "Item {$itemNum}: Quantity cannot exceed 999,999";
                    }
                }
            }

            // Validate totals
            $subtotal = $invoice->getSubtotal();
            if ($subtotal < 0) {
                $errors[] = 'Subtotal cannot be negative';
            }

            $total = $invoice->getTotal();
            if ($total < 0) {
                $errors[] = 'Total cannot be negative';
            }

            // Validate discount
            $discount = $invoice->toArray()['discount'];
            if ($discount < 0) {
                $errors[] = 'Discount cannot be negative';
            } elseif ($discount > $subtotal) {
                $errors[] = 'Discount cannot exceed subtotal';
            }

        } catch (Exception $e) {
            $errors[] = 'Validation error: ' . $e->getMessage();
        }

        return $errors;
    }
}
