<?php
$sock = '/usr/local/nagios/var/rw/live'; //path to the Nagios Livestatus UNIX socket
$jsonFile ='../json/nagios_data.json'; 

//convert the numeric status to a readable status
function hostStateToText($state) {
    return match ((int)$state) {
        0 => "UP",
        1 => "DOWN",
        2 => "UNREACHABLE",
        default => "UNKNOWN"
    };
}
function serviceStateToText($state) {
    return match ((int)$state) {
        0 => "OK", 
        1 => "WARNING",
        2 => "CRITICAL",
        3 => "UNKNOWN",
        default => "UNKNOWN"
    };
}
//Converts a timestamp into readable date & time
function formatTimestamp($ts) {
    if (is_numeric($ts) && $ts > 0) {
        return date("Y-m-d H:i:s", (int)$ts);
    }
    return "N/A";
}
//same for the duration
//we will convert the duration to Xd Xh Xm Xs
function formatDuration($seconds) {
    if (!is_numeric($seconds) || $seconds <= 0) return "0s";

    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    $parts = [];
    if ($days > 0) $parts[] = "{$days}d";
    if ($hours > 0) $parts[] = "{$hours}h";
    if ($minutes > 0) $parts[] = "{$minutes}m";
    if ($secs > 0 || empty($parts)) $parts[] = "{$secs}s";

    return implode(" ", $parts);
}

function livestatus_query(string $sock, string $query): ?string {
    $socket = @stream_socket_client("unix://$sock", $errno, $errstr, 5);
    if (!$socket) {
        error_log("Failed to connect to Livestatus socket: $errstr ($errno)");
        return null;
    }
    fwrite($socket, $query);
    $response = '';
    while (!feof($socket)) {
        $response .= fread($socket, 8192);
    }
    fclose($socket);
    return $response;
}

// --- Hosts ---
$queryHosts = "GET hosts\nColumns: name state last_check last_state_change plugin_output\nOutputFormat: json\n\n";
$responseHosts = livestatus_query($sock, $queryHosts);
$hosts = [];
if ($responseHosts) {
    $hostsRaw = json_decode($responseHosts, true);
    if (is_array($hostsRaw)) {
        foreach ($hostsRaw as $item) {
            // Indices : name=0, state=1, last_check=2, last_state_change=3, plugin_output=4
            $host = trim($item[0] ?? '') ?: "N/A";
            $state = isset($item[1]) ? hostStateToText($item[1]) : "UNKNOWN";
            $last_check = formatTimestamp($item[2] ?? 0);
            $duration_seconds = time() - ($item[3] ?? time());
            $duration = formatDuration($duration_seconds);
            $status_info = trim($item[4] ?? '') ?: "No status info";

            $hosts[] = [
                "host" => $host,
                "status" => $state,
                "last_check" => $last_check,
                "duration" => $duration,
                "status_info" => $status_info,
            ];
        }
    }
}

// --- Services ---
$queryServices = "GET services\nColumns: host_name description state last_check last_state_change state_type plugin_output\nOutputFormat: json\n\n";
$responseServices = livestatus_query($sock, $queryServices);
$services = [];
if ($responseServices) {
    $servicesRaw = json_decode($responseServices, true);
    if (is_array($servicesRaw)) {
        foreach ($servicesRaw as $item) {
            // index: host=0, description=1, state=2, last_check=3, last_state_change=4, state_type=5, plugin_output=6
            $host = trim($item[0] ?? '') ?: "N/A";
            $service = trim($item[1] ?? '') ?: "N/A";
            $status = serviceStateToText($item[2] ?? 3);
            $last_check = formatTimestamp($item[3] ?? 0);
            $duration_seconds = time() - ($item[4] ?? time());
            $duration = formatDuration($duration_seconds);
            $attempt = ((int)($item[5] ?? 1) === 0) ? "SOFT" : "HARD";
            $status_info = trim($item[6] ?? '') ?: "No status info";

            $services[] = [
                "host" => $host,
                "service" => $service,
                "status" => $status,
                "last_check" => $last_check,
                "duration" => $duration,
                "attempt" => $attempt,
                "status_info" => $status_info,
            ];
        }
    }
}
//count the number ofbackends
$queryBackends = "GET status\nColumns: livestatus_version\nOutputFormat: json\n\n";

$responseBackends = livestatus_query($sock, $queryBackends);

$backends_count = 0;
if ($responseBackends) {
    $backendsRaw = json_decode($responseBackends, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($backendsRaw[0][0])) {
        $backends_count = (int) $backendsRaw[0][0]; // Should be 1
    } else {
        error_log("JSON decode error: " . json_last_error_msg());
    }
} else {
    error_log("No response from Livestatus backend");
}

// count the number of Nagios contacts
$queryContacts = "GET contacts\nColumns: name\nOutputFormat: json\n\n";
$responseContacts = livestatus_query($sock, $queryContacts);
$contacts_count = 0;
if ($responseContacts) {
    $contactsRaw = json_decode($responseContacts, true);
    $contacts_count = is_array($contactsRaw) ? count($contactsRaw) : 0;
}
// final results
$data = [
    "success" => true,
    "hosts_count" => count($hosts),
    "services_count" => count($services),
    "backends_count" => $backends_count,
    "contacts_count" => $contacts_count,
    "hosts" => $hosts,
    "services" => $services
];

if (!is_dir(dirname($jsonFile))) {
    mkdir(dirname($jsonFile), 0755, true);
}


file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header('Content-Type: application/json');
echo json_encode($data);



?>
