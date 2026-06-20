<?php
// migrate_database.php - Rebrand and migrate keeprecord database to stockara

echo "Starting database migration from 'keeprecord' to 'stockara'...\n";

$host = '127.0.0.1';
$user = 'root';
$pass = 'mysql';

try {
    // 1. Establish connection to MySQL server
    $pdo = new PDO("mysql:host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✔ Connected to MySQL server successfully.\n";

    // 2. Read database schema from database.sql
    $sqlFile = dirname(__DIR__) . '/database.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("database.sql not found at $sqlFile");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    
    // Rebrand database.sql content to stockara
    $rebrandedSql = str_ireplace('keeprecord', 'stockara', $sqlContent);
    
    echo "✔ Rebranded database.sql content successfully.\n";

    // 3. Execute database schema queries on MySQL
    // Disable foreign key checks and run the schema setup
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    
    // Split queries by semicolon to execute individually
    // Note: This is a simple parser, but works fine for database.sql
    $queries = array_filter(array_map('trim', explode(';', $rebrandedSql)));
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }
    echo "✔ Created database 'stockara' and successfully initialized all rebranded tables.\n";

    // 4. Migrate data table by table from keeprecord to stockara
    // We get tables list from keeprecord
    $stmt = $pdo->query("SHOW TABLES FROM keeprecord");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Migrating table records:\n";
    foreach ($tables as $table) {
        // Clear any default/seed data that was inserted during database initialization if any
        $pdo->exec("TRUNCATE TABLE stockara.$table");
        
        // Copy records
        $stmtCount = $pdo->query("SELECT COUNT(*) FROM keeprecord.$table");
        $rowCount = $stmtCount->fetchColumn();
        
        if ($rowCount > 0) {
            $pdo->exec("INSERT INTO stockara.$table SELECT * FROM keeprecord.$table");
            echo "  - Table '$table': Migrated $rowCount records.\n";
        } else {
            echo "  - Table '$table': Empty (0 records).\n";
        }
    }

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "✔ Successfully enabled foreign key checks.\n";
    echo "✔ Migration completed successfully! Database 'stockara' is ready for use.\n";

} catch (Exception $e) {
    echo "✘ Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
}
