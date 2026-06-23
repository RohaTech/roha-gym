<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$filePath = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)$/i', $uri)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mime = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',  'gif'  => 'image/gif',
            'webp' => 'image/webp','svg'  => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: ' . ($mime[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
    return false;
}

require_once __DIR__ . '/public/index.php';
