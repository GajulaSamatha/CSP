<?php
namespace App;

class Db {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            // Load environment variables from the .env file in the project root
            // This makes sure it works for both web requests and command-line scripts.
            require_once dirname(__DIR__) . '/vendor/autoload.php';
            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();

            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'];
            $db   = $_ENV['DB_DATABASE'];
            $user = $_ENV['DB_USERNAME'];
            $pass = $_ENV['DB_PASSWORD'];

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$connection = new \PDO($dsn, $user, $pass, $options);
        }
        return self::$connection;
    }
}