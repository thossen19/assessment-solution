<?php

namespace InvoiceSystem;

use InvoiceSystem\Interfaces\PDFGeneratorInterface;
use InvoiceSystem\Interfaces\InvoiceInterface;

// Only require vendor/autoload.php if it exists (for PDF generation)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * PDF Generator
 * 
 * Generates professional PDF invoices from invoice data using Dompdf library.
 * Provides HTML to PDF conversion with CSS styling and proper formatting.
 * 
 * Features:
 * - Professional invoice templates with CSS styling
 * - Automatic directory creation for PDF output
 * - Graceful fallback when dependencies unavailable
 * - HTML export as fallback option
 * - Error handling and logging
 * 
 * @package InvoiceSystem
 * @author  Invoice System Team
 * @version 1.0.0
 * @since   1.0.0
 * 
 * @example
 * <code>
 * try {
 *     $pdfGenerator = new PDFGenerator();
 *     $pdfFile = $pdfGenerator->generatePDF($invoice);
 *     echo "PDF generated: {$pdfFile}\n";
 * } catch (Exception $e) {
 *     echo "PDF generation unavailable: " . $e->getMessage() . "\n";
 * }
 * </code>
 */
class PDFGenerator implements PDFGeneratorInterface
{

    /**
     * Dompdf instance for PDF generation
     * 
     * @var Dompdf
     */
    private $dompdf;
    
    /**
     * Dompdf configuration options
     * 
     * @var Options
     */
    private $options;

    public function __construct() {
        // Check if Dompdf is available
        if (!class_exists('Dompdf\Dompdf')) {
            throw new Exception(
                "PDF generation not available. Please run 'composer install' to install Dompdf library."
            );
        }
        
        $this->options = new Options();
        $this->options->set('defaultFont', 'Arial');
        $this->options->set('isRemoteEnabled', true);
        $this->dompdf = new Dompdf($this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function generatePDF(InvoiceInterface $invoice): string {
        try {
            $html = $this->generateHTML($invoice);
            $this->dompdf->loadHtml($html);
            $this->dompdf->setPaper('A4', 'portrait');
            $this->dompdf->render();

            $filename = 'invoices/invoice_' . $invoice->getId() . '.pdf';
            
            // Ensure directory exists
            $dir = dirname($filename);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $pdfContent = $this->dompdf->output();
            if ($pdfContent === false) {
                throw new Exception("Failed to generate PDF content");
            }

            $result = file_put_contents($filename, $pdfContent, LOCK_EX);
            if ($result === false) {
                throw new Exception("Failed to save PDF file: {$filename}");
            }

            return $filename;

        } catch (Exception $e) {
            ErrorHandler::handlePDFError($invoice->getId(), $e);
            throw new Exception("PDF generation failed: " . $e->getMessage());
        }
    }

    /**
     * Generate HTML version of invoice with styling
     *
     * @param Invoice $invoice
     * @return string HTML content
     */
    private function generateHTML($invoice) {
        $subtotal = 0;
        $itemsHtml = '';

        foreach ($invoice->getItems() as $item) {
            $quantity = isset($item['quantity']) ? $item['quantity'] : $item['qty'];
            $lineTotal = $item['price'] * $quantity;
            $subtotal += $lineTotal;

            $itemsHtml .= '<tr>';
            $itemsHtml .= '<td>' . htmlspecialchars($item['name']) . '</td>';
            $itemsHtml .= '<td>$' . number_format($item['price'], 2) . '</td>';
            $itemsHtml .= '<td>' . $quantity . '</td>';
            $itemsHtml .= '<td>$' . number_format($lineTotal, 2) . '</td>';
            $itemsHtml .= '</tr>';
        }

        $total = $invoice->getTotal();
        $discount = $subtotal - $total;

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Invoice #' . htmlspecialchars($invoice->getId()) . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .invoice-info { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .totals { text-align: right; }
                .totals td { border: none; padding: 5px; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>INVOICE</h1>
                <p>Invoice #' . htmlspecialchars($invoice->getId()) . '</p>
            </div>
            
            <div class="invoice-info">
                <p><strong>Customer:</strong> ' . htmlspecialchars($invoice->getCustomer()) . '</p>
                <p><strong>Date:</strong> ' . htmlspecialchars($invoice->toArray()['created_at']) . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $itemsHtml . '
                </tbody>
            </table>
            
            <div class="totals">
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td>$' . number_format($subtotal, 2) . '</td>
                    </tr>';
        
        if ($discount > 0) {
            $html .= '
                    <tr>
                        <td>Discount:</td>
                        <td>-$' . number_format($discount, 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td><strong>$' . number_format($total, 2) . '</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="footer">
                <p>Thank you for your business!</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function exportHTML(InvoiceInterface $invoice): string {
        $html = $this->generateHTML($invoice);
        $filename = 'invoices/invoice_' . $invoice->getId() . '.html';
        
        // Ensure directory exists
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($filename, $html);
        return $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable(): bool {
        return class_exists('Dompdf\Dompdf');
    }
}
