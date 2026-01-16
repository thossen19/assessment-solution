<?php

namespace InvoiceSystem;

/**
 * Error Handler
 * 
 * Centralized error logging and management for the invoice system.
 * Provides consistent error handling across all components with detailed logging.
 * 
 * Features:
 * - Centralized error logging with context information
 * - Different error levels (ERROR, WARNING, INFO)
 * - File operation error handling
 * - Validation error logging
 * - PDF generation error tracking
 * - Error statistics and reporting
 * - Log file management
 * 
 * @package InvoiceSystem
 * @author  Invoice System Team
 * @version 1.0.0
 * @since   1.0.0
 * 
 * @example
 * <code>
 * try {
 *     $invoice->saveToFile();
 * } catch (Exception $e) {
 *     ErrorHandler::handleFileError('save', 'invoices.json', $e);
 * }
 * 
 * // Log custom error
 * ErrorHandler::logError('Custom error message', 'ERROR', ['context' => 'data']);
 * </code>
 */
class ErrorHandler {

    /**
     * Path to the error log file
     * 
     * @var string
     */
    private static $logFile = 'data/error_log.txt';

    /**
     * Log an error with context information
     * 
     * Creates a structured log entry with timestamp, error level, message,
     * and optional context data. Uses file locking for thread safety.
     * 
     * @param string $message  Error message to log
     * @param string $level    Error level (ERROR, WARNING, INFO)
     * @param array  $context  Additional context data (optional)
     * 
     * @return void
     */
    public static function logError(string $message, string $level = 'ERROR', array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}\n";
        
        // Ensure directory exists
        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Handle file operation errors
     *
     * @param string $operation File operation being performed
     * @param string $filename File name
     * @param Exception $e Exception that occurred
     */
    public static function handleFileError($operation, $filename, $e) {
        self::logError(
            "File operation failed: {$operation} on {$filename}",
            'ERROR',
            [
                'operation' => $operation,
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]
        );
    }

    /**
     * Handle validation errors
     *
     * @param array $errors Array of validation errors
     * @param mixed $data The data that failed validation
     */
    public static function handleValidationErrors($errors, $data = null) {
        self::logError(
            "Validation failed: " . implode('; ', $errors),
            'WARNING',
            [
                'errors' => $errors,
                'data' => $data
            ]
        );
    }

    /**
     * Handle database/JSON operation errors
     *
     * @param string $operation Database operation
     * @param string $query Query or operation details
     * @param Exception $e Exception that occurred
     */
    public static function handleDataError($operation, $query, $e) {
        self::logError(
            "Data operation failed: {$operation}",
            'ERROR',
            [
                'operation' => $operation,
                'query' => $query,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]
        );
    }

    /**
     * Handle PDF generation errors
     *
     * @param string $invoiceId Invoice ID
     * @param Exception $e Exception that occurred
     */
    public static function handlePDFError($invoiceId, $e) {
        self::logError(
            "PDF generation failed for invoice {$invoiceId}",
            'ERROR',
            [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]
        );
    }

    /**
     * Get recent error log entries
     *
     * @param int $limit Number of entries to return
     * @return array Array of log entries
     */
    public static function getRecentErrors($limit = 50) {
        if (!file_exists(self::$logFile)) {
            return [];
        }

        $lines = file(self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $recentLines = array_slice($lines, -$limit);
        
        $entries = [];
        foreach ($recentLines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+): (.+)$/', $line, $matches)) {
                $entries[] = [
                    'timestamp' => $matches[1],
                    'level' => $matches[2],
                    'message' => $matches[3]
                ];
            }
        }
        
        return array_reverse($entries);
    }

    /**
     * Clear error log
     */
    public static function clearLog() {
        if (file_exists(self::$logFile)) {
            unlink(self::$logFile);
        }
    }

    /**
     * Get error statistics
     *
     * @return array Error statistics
     */
    public static function getErrorStats() {
        if (!file_exists(self::$logFile)) {
            return [
                'total_errors' => 0,
                'by_level' => [],
                'recent_24h' => 0
            ];
        }

        $lines = file(self::$logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $stats = [
            'total_errors' => count($lines),
            'by_level' => [],
            'recent_24h' => 0
        ];

        $yesterday = time() - (24 * 60 * 60);
        
        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+):/', $line, $matches)) {
                $level = $matches[2];
                $timestamp = strtotime($matches[1]);
                
                // Count by level
                if (!isset($stats['by_level'][$level])) {
                    $stats['by_level'][$level] = 0;
                }
                $stats['by_level'][$level]++;
                
                // Count recent errors
                if ($timestamp > $yesterday) {
                    $stats['recent_24h']++;
                }
            }
        }
        
        return $stats;
    }
}
