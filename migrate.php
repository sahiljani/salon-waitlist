<?php
require_once 'config.php';

function ensureMigrationsTable(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        batch INT NOT NULL,
        applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function listMigrationFiles(string $path): array {
    $files = glob($path . '/*.php') ?: [];
    sort($files);
    return $files;
}

function loadMigration(string $file): array {
    $migration = require $file;
    if (!is_array($migration) || !isset($migration['up']) || !is_array($migration['up'])) {
        throw new RuntimeException("Invalid migration file: {$file}");
    }
    return $migration;
}

function appliedMigrations(PDO $pdo): array {
    $stmt = $pdo->query("SELECT migration FROM migrations");
    return array_flip(array_map(fn($r) => $r['migration'], $stmt->fetchAll(PDO::FETCH_ASSOC)));
}

function nextBatchNumber(PDO $pdo): int {
    $stmt = $pdo->query("SELECT COALESCE(MAX(batch), 0) AS max_batch FROM migrations");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return ((int)($row['max_batch'] ?? 0)) + 1;
}

function applyMigration(PDO $pdo, string $name, array $sqlList, int $batch): void {
    $pdo->beginTransaction();
    try {
        foreach ($sqlList as $sql) {
            $pdo->exec($sql);
        }
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$name, $batch]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

try {
    $pdo = getDB();
    ensureMigrationsTable($pdo);

    $migrationPath = __DIR__ . '/db/migrations';
    $files = listMigrationFiles($migrationPath);
    $applied = appliedMigrations($pdo);
    $batch = nextBatchNumber($pdo);

    $ran = 0;
    foreach ($files as $file) {
        $name = basename($file);
        if (isset($applied[$name])) {
            continue;
        }

        $migration = loadMigration($file);
        applyMigration($pdo, $name, $migration['up'], $batch);
        $ran++;
        echo "Applied: {$name}\n";
    }

    if ($ran === 0) {
        echo "No pending migrations.\n";
    } else {
        echo "Migration batch {$batch} completed ({$ran} migration(s)).\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}
