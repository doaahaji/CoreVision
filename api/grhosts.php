<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$socketPath = "/usr/local/nagios/var/rw/live";

// Récupération des services groupés par hôte
$queryServices = <<<EOT
GET services
Columns: host_name state
EOT;

$fp1 = fsockopen("unix://$socketPath", -1, $errno, $errstr);
if (!$fp1) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur connexion Livestatus services : $errstr ($errno)"]);
    exit;
}
fwrite($fp1, $queryServices . "\n\n");

$rawServices = "";
while (!feof($fp1)) {
    $rawServices .= fgets($fp1, 4096);
}
fclose($fp1);

// Comptage des états des services par hôte
$serviceStatesPerHost = [];

foreach (explode("\n", trim($rawServices)) as $line) {
    if (empty($line)) continue;
    [$host, $stateCode] = explode(";", $line);

    $stateName = match ((int)$stateCode) {
        0 => "OK",
        1 => "WARNING",
        2 => "CRITICAL",
        3 => "UNKNOWN",
        default => "UNREACHABLE"
    };

    if (!isset($serviceStatesPerHost[$host])) {
        $serviceStatesPerHost[$host] = [];
    }
    if (!isset($serviceStatesPerHost[$host][$stateName])) {
        $serviceStatesPerHost[$host][$stateName] = 0;
    }
    $serviceStatesPerHost[$host][$stateName]++;
}

// Récupération des hôtes
$queryHosts = <<<EOT
GET hosts
Columns: name groups state plugin_output last_check
EOT;

$fp2 = fsockopen("unix://$socketPath", -1, $errno, $errstr);
if (!$fp2) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur connexion Livestatus hosts : $errstr ($errno)"]);
    exit;
}
fwrite($fp2, $queryHosts . "\n\n");

$rawHosts = "";
while (!feof($fp2)) {
    $rawHosts .= fgets($fp2, 4096);
}
fclose($fp2);

// Traitement final
$lines = explode("\n", trim($rawHosts));
$data = [];

foreach ($lines as $line) {
    if (empty($line)) continue;
    [$name, $groupStr, $hostState, $pluginOutput, $lastCheck] = explode(";", $line);

    $groups = array_filter(array_map('trim', explode(",", $groupStr)));

    // Résumé lisible de plugin_output
    $rawInfo = strtolower(trim($pluginOutput));
    $status_info = match (true) {
        str_contains($rawInfo, "connection refused") => "Connexion refusée",
        str_contains($rawInfo, "timed out") => "Temps dépassé",
        str_contains($rawInfo, "unreachable") => "Injoignable",
        str_contains($rawInfo, "ok") => "Fonctionnel",
        str_contains($rawInfo, "critical") => "Critique",
        str_contains($rawInfo, "warning") => "Avertissement",
        default => mb_strimwidth($pluginOutput, 0, 40, "…")
    };

    // Formatage des états de services
    $serviceSummary = "-";
    $serviceCount = 0;

    if (isset($serviceStatesPerHost[$name])) {
        $parts = [];
        foreach ($serviceStatesPerHost[$name] as $state => $count) {
            $parts[] = "$count $state";
            $serviceCount += $count;
        }
        $serviceSummary = implode(", ", $parts);
    }

    // Pour chaque groupe → une ligne
    foreach ($groups as $groupName) {
        $data[] = [
            "group" => $groupName,
            "name" => $name,
            "host_status" => mapHostState($hostState),
            "service_count" => $serviceCount,
            "service_status" => $serviceSummary,
            "detail" => ucfirst($status_info),
            "last" => date("d/m/Y H:i:s", (int)$lastCheck)
        ];
    }
}

function mapHostState($code) {
    return match ((int)$code) {
        0 => "UP",
        1 => "DOWN",
        2 => "UNREACHABLE",
        default => "UNKNOWN"
    };
}

header("Content-Type: application/json");
echo json_encode($data);
