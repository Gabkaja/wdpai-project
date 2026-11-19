<?php

declare(strict_types=1);

namespace App\Service;

final class AvatarProcessor
{
    private const MAX_BYTES = 2097152;
    private const MAX_DIMENSION = 4000;
    private const TARGET_SIZE = 256;
    private const QUALITY = 85;
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp'];

    public function process(string $tmpPath, int $uploadError, int $size): string
    {
        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            throw new \DomainException('avatar_missing');
        }

        if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE) {
            throw new \DomainException('avatar_too_large');
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            throw new \DomainException('avatar_upload_failed');
        }

        if ($size <= 0) {
            throw new \DomainException('avatar_missing');
        }

        if ($size > self::MAX_BYTES) {
            throw new \DomainException('avatar_too_large');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpPath);

        if (!is_string($mime) || !in_array($mime, self::ALLOWED_MIME, true)) {
            throw new \DomainException('avatar_invalid_type');
        }

        $info = @getimagesize($tmpPath);

        if ($info === false) {
            throw new \DomainException('avatar_invalid_image');
        }

        $width = (int) $info[0];
        $height = (int) $info[1];

        if ($width <= 0 || $height <= 0) {
            throw new \DomainException('avatar_invalid_image');
        }

        if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            throw new \DomainException('avatar_dimensions_too_large');
        }

        $bytes = file_get_contents($tmpPath);

        if ($bytes === false) {
            throw new \DomainException('avatar_invalid_image');
        }

        $image = @imagecreatefromstring($bytes);

        if ($image === false) {
            throw new \DomainException('avatar_invalid_image');
        }

        $canvas = null;

        try {
            $side = min($width, $height);
            $sourceX = intdiv($width - $side, 2);
            $sourceY = intdiv($height - $side, 2);

            $canvas = imagecreatetruecolor(self::TARGET_SIZE, self::TARGET_SIZE);

            if ($canvas === false) {
                throw new \DomainException('avatar_processing_failed');
            }

            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);

            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);

            if ($transparent === false || !imagefill($canvas, 0, 0, $transparent)) {
                throw new \DomainException('avatar_processing_failed');
            }

            if (!imagecopyresampled($canvas, $image, 0, 0, $sourceX, $sourceY, self::TARGET_SIZE, self::TARGET_SIZE, $side, $side)) {
                throw new \DomainException('avatar_processing_failed');
            }

            ob_start();
            $encoded = @imagewebp($canvas, null, self::QUALITY);
            $data = ob_get_clean();

            if (!$encoded || $data === false || $data === '') {
                throw new \DomainException('avatar_processing_failed');
            }

            return $data;
        } finally {
            imagedestroy($image);

            if ($canvas !== null && $canvas !== false) {
                imagedestroy($canvas);
            }
        }
    }
}
