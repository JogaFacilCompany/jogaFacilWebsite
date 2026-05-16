<?php
// utils/imageUpload.php
require_once __DIR__ . '/../config/database.php';

function processArenaImages(int $arenaId, array $files) {
    $uploadDir = __DIR__ . '/../assets/uploads/quadras/';
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $pdo = getDbConnection();

    // 1. Capa
    if (isset($files['imagemCapa']) && $files['imagemCapa']['error'] === UPLOAD_ERR_OK) {
        $tmpFile = $files['imagemCapa']['tmp_name'];
        $fileSize = $files['imagemCapa']['size'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpFile);

        if ($fileSize <= $maxFileSize && in_array($mimeType, $allowedMimes)) {
            $sourceImage = createImageResourceArena($tmpFile, $mimeType);
            if ($sourceImage) {
                $outputImage = resizeAndCropArena($sourceImage, 1200, 400);
                $fileName = 'capa_' . $arenaId . '_' . time() . '.jpg';
                $filePath = $uploadDir . $fileName;

                if (imagejpeg($outputImage, $filePath, 85)) {
                    // Remove old capa
                    $stmt = $pdo->prepare("SELECT imagem FROM quadras WHERE id = ?");
                    $stmt->execute([$arenaId]);
                    $old = $stmt->fetchColumn();
                    if ($old && file_exists($uploadDir . $old)) {
                        unlink($uploadDir . $old);
                    }
                    
                    $updStmt = $pdo->prepare("UPDATE quadras SET imagem = ? WHERE id = ?");
                    $updStmt->execute([$fileName, $arenaId]);
                }
                imagedestroy($sourceImage);
                imagedestroy($outputImage);
            }
        }
    }

    // 2. Galeria
    if (isset($files['imagensGaleria']) && is_array($files['imagensGaleria']['tmp_name'])) {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM quadra_imagens WHERE quadra_id = ?");
        $countStmt->execute([$arenaId]);
        $currentImagesCount = $countStmt->fetchColumn();
        
        $maxNewImages = 6 - $currentImagesCount;

        if ($maxNewImages > 0) {
            $filesCount = count($files['imagensGaleria']['tmp_name']);
            for ($i = 0; $i < $filesCount && $i < $maxNewImages; $i++) {
                if ($files['imagensGaleria']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpFile = $files['imagensGaleria']['tmp_name'][$i];
                    $fileSize = $files['imagensGaleria']['size'][$i];
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($tmpFile);

                    if ($fileSize <= $maxFileSize && in_array($mimeType, $allowedMimes)) {
                        $sourceImage = createImageResourceArena($tmpFile, $mimeType);
                        if ($sourceImage) {
                            $outputImage = resizeAndCropArena($sourceImage, 800, 600);
                            $fileName = 'galeria_' . $arenaId . '_' . time() . '_' . $i . '.jpg';
                            $filePath = $uploadDir . $fileName;

                            if (imagejpeg($outputImage, $filePath, 85)) {
                                $insStmt = $pdo->prepare("INSERT INTO quadra_imagens (quadra_id, nome_arquivo) VALUES (?, ?)");
                                $insStmt->execute([$arenaId, $fileName]);
                            }
                            imagedestroy($sourceImage);
                            imagedestroy($outputImage);
                        }
                    }
                }
            }
        }
    }
}

function createImageResourceArena($tmpFile, $mimeType) {
    switch ($mimeType) {
        case 'image/jpeg': return imagecreatefromjpeg($tmpFile);
        case 'image/png': return imagecreatefrompng($tmpFile);
        case 'image/webp': return imagecreatefromwebp($tmpFile);
        case 'image/gif': return imagecreatefromgif($tmpFile);
        default: return false;
    }
}

function resizeAndCropArena($sourceImage, $targetWidth, $targetHeight) {
    $srcWidth = imagesx($sourceImage);
    $srcHeight = imagesy($sourceImage);

    $srcRatio = $srcWidth / $srcHeight;
    $targetRatio = $targetWidth / $targetHeight;

    $newWidth = $targetWidth;
    $newHeight = $targetHeight;
    $srcX = 0;
    $srcY = 0;

    if ($srcRatio > $targetRatio) {
        $cropWidth = (int)($srcHeight * $targetRatio);
        $srcX = (int)(($srcWidth - $cropWidth) / 2);
        $srcWidth = $cropWidth;
    } else {
        $cropHeight = (int)($srcWidth / $targetRatio);
        $srcY = (int)(($srcHeight - $cropHeight) / 2);
        $srcHeight = $cropHeight;
    }

    $outputImage = imagecreatetruecolor($targetWidth, $targetHeight);
    $white = imagecolorallocate($outputImage, 255, 255, 255);
    imagefill($outputImage, 0, 0, $white);

    imagecopyresampled(
        $outputImage, $sourceImage,
        0, 0, $srcX, $srcY,
        $targetWidth, $targetHeight,
        $srcWidth, $srcHeight
    );

    return $outputImage;
}
