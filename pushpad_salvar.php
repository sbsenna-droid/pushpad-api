<?php

header("Content-Type: application/json");

// ---------------------------------------------------
// LER JSON DO FRONT
// ---------------------------------------------------
$data = json_decode(file_get_contents("php://input"), true);

$endpoint = $data["endpoint"] ?? null;
$keys     = $data["keys"] ?? null;
$usuario  = $data["usuario"] ?? null;

if (!$endpoint || !$keys || !$usuario) {
    echo json_encode(["erro" => "Dados incompletos"]);
    exit;
}

// ---------------------------------------------------
// CRIAR PAYLOAD PARA PUSHPAD
// ---------------------------------------------------
$payload = [
    "subscription" => [
        "endpoint" => $endpoint,
        "keys" => [
            "p256dh" => $keys["p256dh"] ?? "",
            "auth"   => $keys["auth"] ?? ""
        ]
    ]
];

$ch = curl_init("https://api.pushpad.xyz/subscriptions");

curl_setopt($ch, CURLOPT_USERPWD, "8xS6g1Dp3mVy0AEqIR2dGi1MhP9omB3izCOf9jop:");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Fixes importantes
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($http !== 201) {
    echo json_encode(["erro" => "falha_pushpad", "http" => $http, "res" => $res]);
    exit;
}

$sub = json_decode($res, true);

echo json_encode([
    "sucesso" => true,
    "push_user_id" => $sub["id"],
    "usuario" => $usuario
]);
