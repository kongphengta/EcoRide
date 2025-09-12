<?php
// Affiche les 100 dernières lignes du log de production Symfony
$logFile = __DIR__ . '/var/log/prod.log';
if (!file_exists($logFile)) {
    echo "Fichier de log non trouvé : $logFile";
    exit;
}

$lines = file($logFile);
$lastLines = array_slice($lines, -100);
echo "<pre>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";
