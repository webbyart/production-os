<?php
/**
 * Export Helper
 * Handle Excel and PDF exports
 */

class ExportHelper
{
    /**
     * Export to CSV
     */
    public static function exportCSV($filename, $headers, $data)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, $headers);
        
        // Add data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export to Excel (simple CSV format)
     */
    public static function exportExcel($filename, $headers, $data)
    {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');

        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, $headers, "\t");
        
        // Add data
        foreach ($data as $row) {
            fputcsv($output, $row, "\t");
        }
        
        fclose($output);
        exit;
    }

    /**
     * Generate HTML table from data
     */
    public static function generateHTMLTable($title, $headers, $data)
    {
        $html = '<table border="1" style="border-collapse: collapse; width: 100%; margin-top: 20px;">';
        $html .= '<thead><tr style="background-color: #f0f0f0;">';
        
        foreach ($headers as $header) {
            $html .= '<th style="padding: 8px; text-align: left;">' . $header . '</th>';
        }
        
        $html .= '</tr></thead><tbody>';
        
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td style="padding: 8px;">' . $cell . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Generate PDF content (as HTML)
     */
    public static function generatePDFContent($title, $subtitle, $headers, $data, $generatedDate = true)
    {
        $html = '<!DOCTYPE html>';
        $html .= '<html>';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>' . $title . '</title>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 20px; }';
        $html .= 'h1 { color: #333; text-align: center; }';
        $html .= 'h3 { color: #666; text-align: center; }';
        $html .= '.date { text-align: right; color: #999; font-size: 12px; }';
        $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }';
        $html .= 'th { background-color: #4472C4; color: white; }';
        $html .= 'tr:nth-child(even) { background-color: #f9f9f9; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        
        $html .= '<h1>' . $title . '</h1>';
        if ($subtitle) {
            $html .= '<h3>' . $subtitle . '</h3>';
        }
        
        if ($generatedDate) {
            $html .= '<p class="date">Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        }
        
        $html .= '<table>';
        $html .= '<thead><tr>';
        
        foreach ($headers as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        
        $html .= '</tr></thead><tbody>';
        
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $cell . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        $html .= '</body>';
        $html .= '</html>';
        
        return $html;
    }
}
