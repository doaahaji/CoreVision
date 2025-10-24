<?php
header('Content-Type: application/json');

//  Sécuriser : supprimer erreurs HTML dans la réponse JSON
ob_clean();

// Socket Livestatus (ajuste si besoin)
$sock_path = "/usr/local/nagios/var/rw/live";

// Fonction requête vers Livestatus
function queryLivestatus($query, $socket) {
  $sock = @fsockopen("unix://$socket", -1, $errno, $errstr);
  if (!$sock) return false;

  fwrite($sock, $query);
  $output = '';
  while (!feof($sock)) {
    $output .= fgets($sock, 4096);
  }
  fclose($sock);
  return $output;
}

// Requêtes
$services_query = <<<QUERY
GET services
Columns: host_name description state last_check
Filter: state != 0
Sort: -last_check
Limit: 3
\n
QUERY;

$hosts_query = <<<QUERY
GET hosts
Columns: name state last_check
Filter: state != 0
Sort: -last_check
Limit: 3
\n
QUERY;

$problems = [];

// 🔄 Services
$services_result = queryLivestatus($services_query, $sock_path);
if ($services_result !== false) {
  $lines = explode("\n", trim($services_result));
  foreach ($lines as $line) {
    $parts = explode(';', $line);
    if (count($parts) >= 4) {
      list($host, $desc, $state, $check_time) = $parts;
      $problems[] = [
        'type' => 'Service',
        'name' => "$desc sur $host",
        'time' => 'depuis ' . date('H:i:s', (int)$check_time)
      ];
    }
  }
}

// 🔄 Hosts
$hosts_result = queryLivestatus($hosts_query, $sock_path);
if ($hosts_result !== false) {
  $lines = explode("\n", trim($hosts_result));
  foreach ($lines as $line) {
    $parts = explode(';', $line);
    if (count($parts) >= 3) {
      list($host, $state, $check_time) = $parts;
      $problems[] = [
        'type' => 'Host',
        'name' => $host,
        'time' => 'depuis ' . date('H:i:s', (int)$check_time)
      ];
    }
  }
}

// Trier par heure (plus récent en haut)
usort($problems, fn($a, $b) => strcmp($b['time'], $a['time']));

// Garder 3 éléments max
$problems = array_slice($problems, 0, 3);

// ✅ Réponse JSON propre
echo json_encode($problems);
exit;

