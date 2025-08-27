<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class QrCodeService
{
    /**
     * QRコードをPNG形式で生成
     */
    public function generatePng(string $data, int $size = 300): string
    {
        $qrCode = new QrCode(
            data: $data,
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return $result->getString();
    }

    /**
     * QRコードをSVG形式で生成（ベクター形式）
     */
    public function generateSvg(string $data, int $size = 300): string
    {
        $qrCode = new QrCode(
            data: $data,
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer = new SvgWriter();
        $result = $writer->write($qrCode);

        return $result->getString();
    }

    /**
     * QRコードをファイルとして保存
     */
    public function saveQrCode(string $data, string $filename, string $format = 'png'): string
    {
        $storagePath = storage_path('app/public/qrcodes');
        
        // ディレクトリが存在しない場合は作成
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $filePath = $storagePath . '/' . $filename . '.' . $format;

        if ($format === 'svg') {
            $content = $this->generateSvg($data);
        } else {
            $content = $this->generatePng($data);
        }

        file_put_contents($filePath, $content);

        return $filePath;
    }
}
