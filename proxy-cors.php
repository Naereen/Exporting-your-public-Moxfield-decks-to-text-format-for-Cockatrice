<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

// En-têtes CORS stricts
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    exit(0);
}

// Détermination de l'action demandée
$action = isset($_GET['action']) ? $_GET['action'] : '';

$targetUrl = "";

if ($action === 'list') {
    // Construction de l'URL de liste de manière interne et sûre
    $username = isset($_GET['username']) ? urlencode($_GET['username']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if (empty($username)) {
        http_response_code(400);
        echo json_encode(["error" => "Nom d'utilisateur manquant."]);
        ob_end_flush();
        exit;
    }
    $targetUrl = "https://api.moxfield.com/v2/users/{$username}/decks?pageNumber={$page}&pageSize=100";

} elseif ($action === 'detail') {
    // Construction de l'URL de détail de manière interne et sûre
    $deckId = isset($_GET['deckId']) ? urlencode($_GET['deckId']) : '';
    if (empty($deckId)) {
        http_response_code(400);
        echo json_encode(["error" => "Identifiant de deck manquant."]);
        ob_end_flush();
        exit;
    }
    $targetUrl = "https://api.moxfield.com/v2/decks/all/{$deckId}";

} else {
    http_response_code(400);
    echo json_encode(["error" => "Action non autorisée ou manquante (choix: list, detail)."]);
    ob_end_flush();
    exit;
}

// Requête cURL épurée (On n'essaie pas de trop truquer pour ne pas déclencher Anubis)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

// On utilise un User-Agent neutre ou celui par défaut du serveur de script
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CransProxyBot/1.0;)');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion interne", "details" => curl_error($ch)]);
} else {
    http_response_code($httpCode);
    echo $response;
}

curl_close($ch);
ob_end_flush();
?>