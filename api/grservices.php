<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Chemin du socket Livestatus
$socketPath = "/usr/local/nagios/var/rw/live";

// Requête pour les services avec leurs groupes
$query = <<<EOT
GET services
Columns: host_name description state plugin_output last_check service_groups
EOT;

// 📡 Connexion au socket Livestatus
$fp = fsockopen("unix://$socketPath", -1, $errno, $errstr);
if (!$fp) {
    http_response_code(500);
    echo json_encode(["error" => "Connexion échouée : $errstr ($errno)"]);
    exit;
}

fwrite($fp, $query . "\n\n");

// Lecture brute
$raw = "";
while (!feof($fp)) {
    $raw .= fgets($fp, 4096);
}
fclose($fp);

// Traitement ligne par ligne
$lines = explode("\n", trim($raw));
$data = [];

foreach ($lines as $line) {
    $fields = explode(";", $line);
    if (count($fields) < 6) continue;

    $rawInfo = strtolower(trim($fields[3]));

    // Statut résumé lisible
    $status_info = match (true) {
        str_contains($rawInfo, "connection refused") => "Connexion refusée",
        str_contains($rawInfo, "timed out") => "Temps dépassé",
        str_contains($rawInfo, "unreachable") => "Injoignable",
        str_contains($rawInfo, "ok") => "Fonctionnel",
        str_contains($rawInfo, "critical") => "Critique",
        str_contains($rawInfo, "warning") => "Avertissement",
        default => mb_strimwidth($fields[3], 0, 40, "…")
    };

    // Récupération des groupes de service
    $groups = array_filter(array_map('trim', explode(",", $fields[5])));

    foreach ($groups as $group) {
        $data[] = [
            "group" => $group,
            "service" => $fields[1],
            "state" => mapState($fields[2]),
            "status_info" => ucfirst($status_info),
            "host" => $fields[0],
            "last" => date("d/m/Y H:i:s", (int)$fields[4])
        ];
    }
}

// Conversion numérique -> texte
function mapState($code) {
    return match ((int)$code) {
        0 => "OK",
        1 => "WARNING",
        2 => "CRITICAL",
        3 => "UNKNOWN",
        default => "UNREACHABLE"
    };
}

header("Content-Type: application/json");
echo json_encode($data);
