<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Chemin vers le socket
$socketPath = "/usr/local/nagios/var/rw/live";

// Requête : on demande les groupes de services
$query = "GET services\nColumns: host_name description state plugin_output last_check service_groups\n";

// Connexion au socket
$fp = fsockopen("unix://$socketPath", -1, $errno, $errstr);
if (!$fp) {
    http_response_code(500);
    echo json_encode(["error" => "Connexion au socket Livestatus échouée: $errstr ($errno)"]);
    exit;
}

fwrite($fp, $query);
fwrite($fp, "\n");

// Lecture brute
$raw = "";
while (!feof($fp)) {
    $raw .= fgets($fp, 4096);
}
fclose($fp);

// Décodage ligne par ligne
$lines = explode("\n", trim($raw));
$data = [];

foreach ($lines as $line) {
    if (empty($line)) continue;
    $fields = explode(";", $line);

    if (count($fields) >= 6) {
        // Extraction du groupe
        $serviceGroups = array_filter(array_map('trim', explode(",", $fields[5])));
        $groupName = $serviceGroups[0] ?? "-";

        // Statut résumé
        $rawInfo = strtolower(trim($fields[3]));
        $status_info = match (true) {
            str_contains($rawInfo, "connection refused") => "Connexion refusée",
            str_contains($rawInfo, "timed out") => "Temps dépassé",
            str_contains($rawInfo, "unreachable") => "Injoignable",
            str_contains($rawInfo, "ok") => "Fonctionnel",
            str_contains($rawInfo, "not found") => "Domaine introuvable",
            str_contains($rawInfo, "hostname/address") => "hostname invalide",
            str_contains($rawInfo, "uptime") => "Fonctionnel",
            str_contains($rawInfo, "critical") => "Critique",
            str_contains($rawInfo, "warning") => "Avertissement",
            default => mb_strimwidth($fields[3], 0, 20, "…")
        };

        $data[] = [
            "name" => $fields[0],                          // host_name
            "service" => $fields[1],                       // description
            "state" => mapState($fields[2]),               // état textuel
            "group" => $groupName,                         // groupe de service
            "status_info" => ucfirst($status_info),        // résumé du plugin_output
            "last" => date("d/m/Y H:i:s", (int)$fields[4]) // horodatage
        ];
    }
}

// Conversion état numérique → texte
function mapState($code) {
    return match ((int)$code) {
        0 => "OK",
        1 => "WARNING",
        2 => "CRITICAL",
        3 => "UNKNOWN",
        default => "UNREACHABLE"
    };
}

// Envoi JSON
header("Content-Type: application/json");
echo json_encode($data);
