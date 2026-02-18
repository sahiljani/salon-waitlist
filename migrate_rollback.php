<?php
require_once 'config.php';

function loadMigration(string $file): array {
    $migration = require $file;
    if (!is_array($migration) || !isset($migration['down']) || !is_array($migration['down'])) {
        throw new RuntimeException("Invalid migration file: {$file}");
    }
    return $migration;
}

try {
    $pdo = getDB();

    $exists = $pdo->query("SHOW TABLES LIKE 'migrations'")->fetch();
    if (!$exists) {
        echo "No migrations table found. Nothing to rollback.\n";
        exit(0);
    }

    $batchStmt = $pdo->query("SELECT MAX(batch) AS latest_batch FROM migrations");
    $latestBatch = (int)($batchStmt->fetch(PDO::FETCH_ASSOC)['latest_batch'] ?? 0);
    if ($latestBatch <= 0) {
        echo "No applied migrations found.\n";
        exit(0);
    }

    $stmt = $pdo->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
    $stmt->execute([$latestBatch]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $migrationPath = __DIR__ . '/db/migrations';
    $rolledBack = 0;

    foreach ($rows as $row) {
        $name = $row['migration'];
        $file = $migrationPath . '/' . $name;
        if (!file_exists($file)) {
            throw new RuntimeException("Migration file missing for rollback: {$name}");
        }

        $migration = loadMigration($file);

        $pdo->beginTransaction();
        try {
            foreach ($migration['down'] as $sql) {
                $pdo->exec($sql);
            }
            $del = $pdo->prepare("DELETE FROM migrations WHERE migration = ?");
            $del->execute([$name]);
            $pdo->commit();
            $rolledBack++;
            echo "Rolled back: {$name}\n";
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    echo "Rollback completed for batch {$latestBatch} ({$rolledBack} migration(s)).\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Rollback failed: " . $e->getMessage() . "\n");
    exit(1);
}
