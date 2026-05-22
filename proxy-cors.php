<?php
// 1. Activer la mise en mémoire tampon pour éviter le problème "Headers already sent"
ob_start();

// 2. Désactiver l'affichage des erreurs textuelles qui casseraient le JSON ou les en-têtes
error_reporting(0);
ini_set('display_errors', 0);

// 3. Déclaration agressive des en-têtes CORS
// On force la sortie des en-têtes avant toute exécution
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// Gestion du Preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    exit(0);
}

// 4. Validation du paramètre URL
if (!isset($_GET['url'])) {
    http_response_code(400);
    echo json_encode(["error" => "Paramètre 'url' manquant."]);
    ob_end_flush();
    exit;
}

$targetUrl = $_GET['url'];

if (strpos($targetUrl, 'https://api.moxfield.com/') !== 0) {
    http_response_code(403);
    echo json_encode(["error" => "URL non autorisée."]);
    ob_end_flush();
    exit;
}

// 5. Exécution du cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

$headers = [
    'Accept: application/json, text/plain, */*',
    'Referer: https://www.moxfield.com/',
    'Origin: https://www.moxfield.com'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur proxy cURL", "details" => curl_error($ch)]);
} else {
    http_response_code($httpCode);
    echo $response;
}

curl_close($ch);

// Flush du tampon de sortie de manière propre
ob_end_flush();
?>