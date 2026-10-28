<?php
require __DIR__ . '/vendor/autoload.php';

echo "--- Autoloader Verification Script ---\n\n";

$tests = [
    'Handlers' => 'Handlers\\BorrowersHandler'
];

foreach ($tests as $type => $class) {
    if (class_exists($class)) {
        echo "[SUCCESS] {$type} namespace works: {$class} loaded.\n";
    } else {
        echo "[FAILED]  {$type} namespace failed: {$class} not found.\n";
    }
}
?>