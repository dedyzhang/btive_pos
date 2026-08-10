<?php
$lines = file('resources/views/cashflow/index.blade.php');
foreach ($lines as $num => $line) {
    if (strpos($line, '<table') !== false || strpos($line, '<tbody') !== false || strpos($line, 'id="table') !== false || strpos($line, 'DataTable') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
