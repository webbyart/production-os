<?php
/**
 * QR Code Helper
 */

class QRCodeHelper
{
    private $qrLibraryPath;

    public function __construct()
    {
        $this->qrLibraryPath = QRCODE_PATH;
        $this->ensureDirectoryExists();
    }

    /**
     * Ensure QR code directory exists
     */
    private function ensureDirectoryExists()
    {
        if (!is_dir($this->qrLibraryPath)) {
            mkdir($this->qrLibraryPath, 0755, true);
        }
    }

    /**
     * Generate QR code for product
     */
    public function generateProductQR($productCode, $productName)
    {
        $data = "PRODUCT|" . $productCode . "|" . $productName;
        return $this->generateQR($data, "product_" . $productCode);
    }

    /**
     * Generate QR code for raw material
     */
    public function generateMaterialQR($materialCode, $materialName)
    {
        $data = "MATERIAL|" . $materialCode . "|" . $materialName;
        return $this->generateQR($data, "material_" . $materialCode);
    }

    /**
     * Generate QR code for manufacturing order
     */
    public function generateMOQR($moNumber)
    {
        $data = "MO|" . $moNumber;
        return $this->generateQR($data, "mo_" . str_replace('-', '_', $moNumber));
    }

    /**
     * Generate QR code
     */
    private function generateQR($data, $filename)
    {
        // Using a simple encoding method - in production, use a library like:
        // composer require endroid/qr-code
        
        $qrfile = $this->qrLibraryPath . '/' . $filename . '.png';
        
        // For now, return encoded data URL
        // In production, use: https://github.com/endroid/QrCode
        $encoded = urlencode($data);
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $encoded;
        
        return $qrImageUrl;
    }

    /**
     * Get QR code path
     */
    public function getQRPath($filename)
    {
        return $this->qrLibraryPath . '/' . $filename . '.png';
    }
}
