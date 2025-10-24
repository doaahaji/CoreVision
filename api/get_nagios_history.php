<?php
header('Content-Type: application/json');

$logFile = '/usr/local/nagios/var/nagios.log';
$jsonFile = '../json/nagios_history.json';

function parseNagiosLogLine($line) {
    // SERVICE ALERT
    if (preg_match('/^\[(\d+)\] SERVICE ALERT: ([^;]+);([^;]+);([^;]+);([^;]+);(.+)/', trim($line), $matches)) {
        return [
            'timestamp' => (int)$matches[1],
            'type' => 'SERVICE',
            'host' => $matches[2],
            'service' => $matches[3],
            'status' => $matches[4],
            'state_type' => $matches[5],
            'info' => $matches[6]
        ];
    }
    
    // HOST ALERT
    if (preg_match('/^\[(\d+)\] HOST ALERT: ([^;]+);([^;]+);([^;]+);(.+)/', trim($line), $matches)) {
        return [
            'timestamp' => (int)$matches[1],
            'type' => 'HOST',
            'host' => $matches[2],
            'status' => $matches[3],
            'state_type' => $matches[4],
            'info' => $matches[5]
        ];
    }
    
    // FLAPPING ALERT (ignoré dans l'affichage principal)
    if (preg_match('/^\[(\d+)\] (HOST|SERVICE) FLAPPING ALERT: ([^;]+);(?:[^;]+);(.+)/', trim($line), $matches)) {
        return [
            'timestamp' => (int)$matches[1],
            'type' => 'FLAPPING',
            'host' => $matches[3],
            'info' => $matches[4]
        ];
    }
    
    return null;
}

$history = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        $entry = parseNagiosLogLine($line);
        if ($entry && $entry['type'] !== 'FLAPPING') { // On ignore les alertes de flapping
            $dateKey = date('Y-m-d', $entry['timestamp']);
            
            $formattedEntry = [
                'timestamp' => $entry['timestamp'],
                'datetime' => date('Y-m-d H:i:s', $entry['timestamp']),
                'type' => $entry['type'],
                'host' => $entry['host'],
                'status' => $entry['status'],
                'state_type' => $entry['state_type'],
                'info' => $entry['info']
            ];
            
            if ($entry['type'] === 'SERVICE') {
                $formattedEntry['service'] = $entry['service'];
            }
            
            $history[$dateKey][] = $formattedEntry;
        }
    }
}

file_put_contents($jsonFile, json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>