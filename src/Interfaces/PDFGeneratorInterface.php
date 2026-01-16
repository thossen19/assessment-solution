<?php

namespace InvoiceSystem\Interfaces;

/**
 * Interface for PDF generation services
 * 
 * Defines the contract for PDF generation implementations.
 * Allows for different PDF libraries to be used interchangeably.
 * 
 * @package InvoiceSystem\Interfaces
 * @author  Invoice System Team
 * @version 1.0.0
 * @since   1.0.0
 */
interface PDFGeneratorInterface
{
    /**
     * Generate a PDF document from an invoice
     * 
     * @param InvoiceInterface $invoice The invoice to convert to PDF
     * 
     * @return string Path to the generated PDF file
     * @throws \Exception When PDF generation fails
     */
    public function generatePDF(InvoiceInterface $invoice): string;

    /**
     * Export invoice as HTML (fallback when PDF generation is not available)
     * 
     * @param InvoiceInterface $invoice The invoice to convert to HTML
     * 
     * @return string Path to the generated HTML file
     * @throws \Exception When HTML export fails
     */
    public function exportHTML(InvoiceInterface $invoice): string;

    /**
     * Check if PDF generation is available (dependencies installed)
     * 
     * @return bool True if PDF generation is available
     */
    public function isAvailable(): bool;
}
