<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// LÊ O JSON CORRETAMENTE
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['endpoint']) || !isset($input['keys'])) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Dados incompletos",
        "input_recebido" => $input
    ]);
    exit;
}

$usuario = $input['usuario'] ?? null;
$endpoint = $input['endpoint'];
$keys = $input['keys'];

// 1) ENVIA PARA O PUSHPAD
$payload = [
    "subscription" => [
        "endpoint" => $endpoint,
        "keys" => $keys
    ]
];

$ch = curl_init("https://pushpad.xyz/api/v1/subscriptions");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Token token=8xS6g1Dp3mVy0AEqIR2dGi1MhP9omB3izCOf9jop"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resp = curl_exec($ch);
curl_close($ch);

$obj = json_decode($resp, true);

if (!isset($obj["id"])) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Pushpad recusou",
        "resposta" => $obj
    ]);
    exit;
}

$push_user_id = $obj["id"];

// 2) SALVAR NO BANCO (opcional)
// aqui será adicionado depois

echo json_encode([
    "sucesso" => true,
    "push_user_id" => $push_user_id,
    "debug" => [
        "endpoint" => $endpoint,
        "keys" => $keys
    ]
]);
