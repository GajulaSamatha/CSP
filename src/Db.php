<?php
namespace App;

class Db {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo instanceof \PDO) {
            return self::$pdo;
        }

        $host = getenv('DB_HOST') ?: 'db.ntadulxmuxgtnzeewobg.supabase.co';
        $port = getenv('DB_PORT') ?: '5432';
        $db   = getenv('DB_DATABASE') ?: 'postgres';
        $user = getenv('DB_USER') ?: 'postgres';
        $pass = getenv('DB_PASSWORD') ?: '';
        $sslmode = getenv('DB_SSLMODE') ?: 'require';

        $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode={$sslmode}";

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];

        self::$pdo = new \PDO($dsn, $user, $pass, $options);
        return self::$pdo;
    }
}