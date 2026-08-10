<?php
$lines = file('resources/views/cashflow/index.blade.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'modal-transaction') !== false) {
        echo "Line " . ($num + 1) . ": " . trim($line) . "\n";
    }
}
