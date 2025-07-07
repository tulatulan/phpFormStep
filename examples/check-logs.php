<?php
echo "<h2>Log Files Check</h2>";

$logDir = __DIR__ . '/handlers/logs/';
echo "Log Directory: " . $logDir . "<br>";
echo "Directory exists: " . (is_dir($logDir) ? 'YES' : 'NO') . "<br>";
echo "Directory writable: " . (is_writable($logDir) ? 'YES' : 'NO') . "<br>";

if (is_dir($logDir)) {
    $files = scandir($logDir);
    echo "<h3>Files in log directory:</h3>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<strong>$file</strong> (" . filesize($logDir . $file) . " bytes)<br>";
            echo "<pre style='background:#f5f5f5;padding:10px;'>" . htmlspecialchars(file_get_contents($logDir . $file)) . "</pre>";
        }
    }
    
    if (count($files) <= 2) {
        echo "No log files found.<br>";
    }
} else {
    echo "Log directory does not exist!<br>";
}

echo "<h3>Session Data:</h3>";
session_start();
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<a href='demo-v2.php'>Back to Demo</a>";
?>
