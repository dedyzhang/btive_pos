<?php
$file = 'resources/views/activity/history.blade.php';
if (file_exists($file)) {
    $lines = file($file);
    echo "=== $file ===\n";
    foreach ($lines as $num => $line) {
        if (strpos($line, 'delete') !== false || strpos($line, 'destroy') !== false || strpos($line, 'Hapus') !== false || strpos($line, 'trash') !== false || strpos($line, 'button') !== false || strpos($line, 'onclick') !== false) {
            echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
        }
    }
} else {
    echo "File not found\n";
}
