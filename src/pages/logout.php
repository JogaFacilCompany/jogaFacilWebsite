<?php
// pages/logout.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
initSession();
session_destroy();
header('Location: ../index.php');
exit;
