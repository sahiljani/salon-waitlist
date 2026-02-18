<?php
require_once 'config.php';
require_once 'migrations.php';

try {
    $pdo = getDB();
    $steps = runMigrations($pdo);
    foreach ($steps as $step) {
        echo $step . "\n";
    }

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage() . "\n");
}
