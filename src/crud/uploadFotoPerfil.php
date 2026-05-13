<?php
// crud/uploadFotoPerfil.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!isset($_SESSION['usuarioLogado'])) {
        header('Location: ../pages/escolherLogin.php');
        exit;
    }

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    $targetUserId = (int)$_SESSION['usuarioLogado'];
    $uploadDir    = __DIR__ . '/../assets/uploads/perfil/';

    // Validate file upload
    if (!isset($_FILES['fotoPerfil']) || $_FILES['fotoPerfil']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'Arquivo excede o tamanho máximo permitido pelo servidor.',
            UPLOAD_ERR_FORM_SIZE  => 'Arquivo excede o tamanho máximo permitido.',
            UPLOAD_ERR_PARTIAL    => 'Upload incompleto. Tente novamente.',
            UPLOAD_ERR_NO_FILE    => 'Nenhum arquivo selecionado.',
            UPLOAD_ERR_NO_TMP_DIR => 'Erro interno no servidor.',
            UPLOAD_ERR_CANT_WRITE => 'Erro ao salvar o arquivo.',
        ];
        $errorCode = $_FILES['fotoPerfil']['error'] ?? UPLOAD_ERR_NO_FILE;
        $errorMsg  = $uploadErrors[$errorCode] ?? 'Erro desconhecido no upload.';
        setFlash($errorMsg, 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    $tmpFile  = $_FILES['fotoPerfil']['tmp_name'];
    $fileSize = $_FILES['fotoPerfil']['size'];

    // Max 5MB
    if ($fileSize > 5 * 1024 * 1024) {
        setFlash('A imagem deve ter no máximo 5MB.', 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    // Validate MIME type
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo        = new finfo(FILEINFO_MIME_TYPE);
    $mimeType     = $finfo->file($tmpFile);

    if (!in_array($mimeType, $allowedMimes, true)) {
        setFlash('Formato de imagem não suportado. Use JPEG, PNG, WebP ou GIF.', 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    // Load source image based on MIME type
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($tmpFile);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($tmpFile);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($tmpFile);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($tmpFile);
            break;
        default:
            $sourceImage = false;
    }

    if (!$sourceImage) {
        setFlash('Não foi possível processar a imagem. Tente outro arquivo.', 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    // Get crop coordinates from form (sent by Cropper.js)
    $cropX      = (int)($_POST['cropX'] ?? 0);
    $cropY      = (int)($_POST['cropY'] ?? 0);
    $cropWidth  = (int)($_POST['cropWidth'] ?? 0);
    $cropHeight = (int)($_POST['cropHeight'] ?? 0);

    $srcWidth  = imagesx($sourceImage);
    $srcHeight = imagesy($sourceImage);

    // If no crop data or invalid, use full image with center square crop
    if ($cropWidth <= 0 || $cropHeight <= 0) {
        $squareSize = min($srcWidth, $srcHeight);
        $cropX      = (int)(($srcWidth - $squareSize) / 2);
        $cropY      = (int)(($srcHeight - $squareSize) / 2);
        $cropWidth  = $squareSize;
        $cropHeight = $squareSize;
    }

    // Clamp crop values to image boundaries
    $cropX      = max(0, min($cropX, $srcWidth - 1));
    $cropY      = max(0, min($cropY, $srcHeight - 1));
    $cropWidth  = min($cropWidth, $srcWidth - $cropX);
    $cropHeight = min($cropHeight, $srcHeight - $cropY);

    // Create final 200x200 image
    $outputSize   = 200;
    $outputImage  = imagecreatetruecolor($outputSize, $outputSize);

    // Preserve quality
    imagecopyresampled(
        $outputImage, $sourceImage,
        0, 0,                           // dest x, y
        $cropX, $cropY,                 // src x, y
        $outputSize, $outputSize,       // dest width, height
        $cropWidth, $cropHeight         // src width, height
    );

    // Generate unique filename
    $fileName = $targetUserId . '_' . time() . '.jpg';
    $filePath = $uploadDir . $fileName;

    // Save as JPEG quality 85
    if (!imagejpeg($outputImage, $filePath, 85)) {
        imagedestroy($sourceImage);
        imagedestroy($outputImage);
        setFlash('Erro ao salvar a imagem processada.', 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    imagedestroy($sourceImage);
    imagedestroy($outputImage);

    // Delete old photo if exists
    $pdo         = getDbConnection();
    $selectStmt  = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
    $selectStmt->execute([$targetUserId]);
    $currentUser = $selectStmt->fetch();

    if ($currentUser && !empty($currentUser['foto_perfil'])) {
        $oldFilePath = $uploadDir . $currentUser['foto_perfil'];
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }
    }

    // Update database
    $updateStmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
    $updateStmt->execute([$fileName, $targetUserId]);

    // Update session
    $_SESSION['usuarioFoto'] = $fileName;

    setFlash('Foto de perfil atualizada com sucesso!', 'success');
    header('Location: ../pages/perfil.php');
    exit;
} else {
    header('Location: ../pages/perfil.php');
    exit;
}
