<?php
header("Content-Type: application/json; charset=utf-8");

$data = json_decode(file_get_contents("php://input"), true);

$project_id = 9082;
$token      = "8xS6g1Dp3mVy0AEqIR2dGi1MhP9omB3izCOf9jop";

// ---------------------------------------------------------------------
// Caso 1: Registro de subscription
// ---------------------------------------------------------------------
if (isset($data["endpoint"]) && isset($data["keys"])) {

    $url = "https://api.pushpad.xyz/v1/projects/$project_id/subscriptions";

    $payload = [
        "endpoint" => $data["endpoint"],
        "keys"     => $data["keys"],
        "uid"      => $data["uid"]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Token token=$token",
            "Content-Type: application/json"
        ],
    ]);

    $res  = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);

    curl_close($ch);

    echo json_encode([
        "tipo"  => "subscription",
        "http"  => $http,
        "res"   => $res,
        "error" => $err
    ]);

    exit;
}

// ---------------------------------------------------------------------
// Caso 2: Envio de notificação
// ---------------------------------------------------------------------
if (isset($data["notification"])) {

    $url = "https://api.pushpad.xyz/v1/projects/$project_id/notifications";

    $payload = $data["notification"];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Token token=$token",
            "Content-Type: application/json"
        ],
    ]);

    $res  = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);

    curl_close($ch);

    echo json_encode([
        "tipo"  => "notification",
        "http"  => $http,
        "res"   => $res,
        "error" => $err
    ]);

    exit;
}

echo json_encode([
    "http"  => 400,
    "error" => "Payload inválido"
]);
