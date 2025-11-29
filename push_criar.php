<?php

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$project_id   = 9082; // seu project ID
$user_ids     = $data["user_ids"] ?? [];
$title        = $data["titulo"] ?? "Título";
$body         = $data["mensagem"] ?? "Mensagem";
$schedule     = $data["schedule"] ?? null;

$payload = [
    "project_id" => $project_id,
    "notification" => [
        "title" => $title,
        "body"  => $body,
        "custom_data" => ["ok" => 1]
    ],
    "user_ids" => $user_ids,
];

if ($schedule) {
    $payload["schedule"] = $schedule;
}

$ch = curl_init("https://api.pushpad.xyz/notifications");

curl_setopt($ch, CURLOPT_USERPWD, "8xS6g1Dp3mVy0AEqIR2dGi1MhP9omB3izCOf9jop:");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo json_encode([
    "http" => $http,
    "retorno" => json_decode($res, true)
]);
