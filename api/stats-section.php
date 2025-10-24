<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
header('Content-Type: application/json');

// Chemin vers le socket Livestatus
$socketPath = "/usr/local/nagios/var/rw/live";

// Fonction générique de requête
function livestatus_query($query, $socketPath) {
    $fp = @fsockopen("unix://$socketPath", -1, $errno, $errstr);
    if (!$fp) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur de socket Livestatus: $errstr ($errno)"]);
        exit;
    }

    fwrite($fp, $query . "\n\n");
    $raw = '';
    while (!feof($fp)) {
        $raw .= fgets($fp, 4096);
    }
    fclose($fp);
    return $raw;
}

// ----------- HOSTS -----------
$raw_hosts = livestatus_query("GET hosts\nColumns: state", $socketPath);
$lines = explode("\n", trim($raw_hosts));

$up = $down = $unreachable = $pending = 0;
foreach ($lines as $line) {
    if (trim($line) === "") continue;
    $state = (int)trim($line);
    switch ($state) {
        case 0: $up++; break;
        case 1: $down++; break;
        case 2: $unreachable++; break;
        case 3: $pending++; break;
    }
}
$total_hosts = $up + $down + $unreachable + $pending;
$host_health = $total_hosts > 0 ? round(($up / $total_hosts) * 100) : 0;

// ----------- SERVICES -----------
$raw_services = livestatus_query("GET services\nColumns: state", $socketPath);
$lines_services = explode("\n", trim($raw_services));

$total_services = 0;
$services_ok = 0;
foreach ($lines_services as $line) {
    if (trim($line) === "") continue;
    $state = (int)trim($line);
    $total_services++;
    if ($state === 0) {
        $services_ok++;
    }
}
$service_health = $total_services > 0 ? round(($services_ok / $total_services) * 100) : 0;

// ----------- TEMPS MOYEN DE LATENCE -----------
$raw_latencies = livestatus_query("GET services\nColumns: latency\nLimit: 5", $socketPath);
$latency_lines = explode("\n", trim($raw_latencies));
$latencies = array_filter(array_map('floatval', $latency_lines), fn($val) => $val > 0);

$avg_response = count($latencies) > 0
    ? round((array_sum($latencies) * 1000) / count($latencies)) 
    : 0;

// ----------- JSON FINAL -----------
echo json_encode([
    'up' => $up,
    'down' => $down,
    'unreachable' => $unreachable,
    'pending' => $pending,
    'total_hosts' => $total_hosts,
    'total_services' => $total_services,
    'host_health' => $host_health,
    'service_health' => $service_health,
    'avg_response' => (int)$avg_response
]);
