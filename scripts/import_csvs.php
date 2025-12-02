<?php
// Simple CSV importer: creates a text-column table for each CSV (if missing) and inserts rows.
// Run: php scripts\import_csvs.php
require_once __DIR__ . '/../src/Db.php';
use App\Db;

$root = realpath(__DIR__ . '/..');
$csvs = glob($root . '/*.csv');

if (empty($csvs)) {
    echo "No CSV files found in project root.\n";
    exit(0);
}

$pdo = Db::getConnection();

foreach ($csvs as $csvPath) {
    $filename = basename($csvPath);
    $table = preg_replace('/\.[^.]+$/', '', $filename);
    $table = preg_replace('/[^a-z0-9_]/i', '_', $table);
    echo "Importing $filename -> table: $table\n";

    if (($handle = fopen($csvPath, "r")) === false) {
        echo "  Cannot open $csvPath\n";
        continue;
    }

    $headers = fgetcsv($handle);
    if ($headers === false) {
        echo "  Empty CSV\n";
        fclose($handle);
        continue;
    }

    // normalize headers
    $cols = [];
    foreach ($headers as $h) {
        $c = trim($h);
        $c = preg_replace('/[^a-z0-9_]/i', '_', $c);
        if ($c === '') $c = 'col_' . count($cols);
        $cols[] = strtolower($c);
    }

    // create table if not exists - all text columns
    $colDefs = array_map(function($c){ return "\"$c\" text"; }, $cols);
    $createSql = "CREATE TABLE IF NOT EXISTS \"$table\" (" . implode(',', $colDefs) . ");";
    $pdo->exec($createSql);

    // prepare insert
    $placeholders = implode(',', array_fill(0, count($cols), '?'));
    $insertSql = "INSERT INTO \"$table\" (\"" . implode('","', $cols) . "\") VALUES ($placeholders)";
    $stmt = $pdo->prepare($insertSql);

    $rowCount = 0;
    while (($row = fgetcsv($handle)) !== false) {
        // pad row if missing cols
        for ($i = 0; $i < count($cols); $i++) {
            if (!isset($row[$i])) $row[$i] = null;
        }
        $stmt->execute($row);
        $rowCount++;
    }
    fclose($handle);
    echo "  Inserted $rowCount rows into $table\n";
}

echo "All CSV imports finished.\n";