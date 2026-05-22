<?php
/**
 * Proxy CORS & Cloudflare-Bypass pour Moxfield
 * À héberger sur votre serveur PHP.
 */

// 1. Configuration des en-têtes CORS pour autoriser votre GitHub Pages
// Vous pouvez remplacer '*' par 'https://naereen.github.io' pour plus de sécurité
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Gestion de la requête "Preflight" OPTIONS du navigateur
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 2. Vérification du paramètre URL
if (!isset($_GET['url'])) {
    http_response_code(400);
    echo json_encode(["error" => "Paramètre 'url' manquant dans la requête du proxy."]);
    exit;
}

$targetUrl = $_GET['url'];

// Sécurité : On s'assure que le proxy ne sert qu'à requêter l'API Moxfield
if (strpos($targetUrl, 'https://api.moxfield.com/') !== 0) {
    http_response_code(403);
    echo json_encode(["error" => "URL non autorisée. Ce proxy ne dessert que l'API Moxfield."]);
    exit;
}

// 3. Initialisation de cURL avec usurpation de navigateur (User-Agent Spoofing)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

// Configuration d'un en-tête de navigateur moderne très propre
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36';
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

// En-têtes HTTP additionnels requis pour ressembler à une requête humaine
$headers = [
    'Accept: application/json, text/plain, */*',
    'Accept-Language: fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
    'Cache-Control: no-cache',
    'Pragma: no-cache',
    'Referer: https://www.moxfield.com/',
    'Origin: https://www.moxfield.com'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Exécution de la requête
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// 4. Traitement du retour
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode([
        "error" => "Erreur de communication du serveur proxy",
        "details" => curl_error($ch)
    ]);
} else {
    // On relaie fidèlement le code HTTP de Moxfield (200, 404, 429, etc.)
    http_response_code($httpCode);
    echo $response;
}

curl_close($ch);
?>