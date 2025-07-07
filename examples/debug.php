<?php
/**
 * Debug test file
 */

session_start();

echo "<h2>Debug Information</h2>";
echo "<h3>POST Data:</h3>";
echo "<pre>" . print_r($_POST, true) . "</pre>";

echo "<h3>Session Data:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h3>Test Handler:</h3>";

if (!empty($_POST)) {
    // Test include handler
    $_POST['step1'] = 1;
    echo "Including handler...<br>";
    $result = include __DIR__ . '/handlers/handle-step1.php';
    echo "Handler result: " . var_export($result, true) . "<br>";
    
    echo "<h3>Session After Handler:</h3>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    
    echo "<h3>Log Files:</h3>";
    $logDir = __DIR__ . '/handlers/logs/';
    if (is_dir($logDir)) {
        $files = scandir($logDir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<strong>$file:</strong><br>";
                echo "<pre>" . htmlspecialchars(file_get_contents($logDir . $file)) . "</pre>";
            }
        }
    } else {
        echo "Log directory not found!<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Test</title>
</head>
<body>
    <h3>Test Form:</h3>
    <form method="POST">
        <input type="text" name="name" value="Test User" placeholder="Name">
        <input type="email" name="email" value="test@example.com" placeholder="Email">
        <input type="date" name="birth_date" value="1990-01-01">
        <select name="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
        <button type="submit">Test Submit</button>
    </form>
</body>
</html>
