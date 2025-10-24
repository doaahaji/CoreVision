<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$socketPath = "/usr/local/nagios/var/rw/live";
$query = "GET hosts\nColumns: name address groups state plugin_output last_check\n";

$fp = fsockopen("unix://$socketPath", -1, $errno, $errstr);
if (!$fp) {
    http_response_code(500);
    echo json_encode(["error" => "Connexion au socket Livestatus échouée: $errstr ($errno)"]);
    exit;
}

fwrite($fp, $query . "\n\n");

$raw = "";
while (!feof($fp)) {
    $raw .= fgets($fp, 4096);
}
fclose($fp);

$lines = explode("\n", trim($raw));
$data = [];

foreach ($lines as $line) {
    if (empty($line)) continue;
    $fields = explode(";", $line);

    if (count($fields) >= 6) {
        [$name, $address, $groupStr, $state, $pluginOutput, $lastCheck] = $fields;

        // Nettoyage et regroupement des groupes
        $groups = array_filter(array_map('trim', explode(",", $groupStr)));
        $groupName = implode(", ", $groups) ?: "-";

        // Simplification du statut
        $rawInfo = strtolower(trim($pluginOutput));
        $status_info = match (true) {
            str_contains($rawInfo, "connection refused") => "Connexion refusée",
            str_contains($rawInfo, "timed out") => "Temps dépassé",
            str_contains($rawInfo, "unreachable") => "Injoignable",
            str_contains($rawInfo, "ok") => "Fonctionnel",
            str_contains($rawInfo, "critical") => "Critique",
            str_contains($rawInfo, "warning") => "Avertissement",
            default => mb_strimwidth($pluginOutput, 0, 40, '...')
        };

        $data[] = [
            "name" => $name,
            "ip" => $address,
            "group" => $groupName,
            "state" => $state,
            "info" => ucfirst($status_info),
            "last_check" => (int)$lastCheck
        ];
    }
}

header("Content-Type: application/json");
echo json_encode($data);
