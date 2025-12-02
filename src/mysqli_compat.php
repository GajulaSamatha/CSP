<?php
// Compatibility shim that uses PDO when mysqli isn't available.
// Safe to include even if PHP's mysqli extension is present.

require_once __DIR__ . '/Db.php';
use App\Db;

if (!class_exists('mysqli_result_wrapper')) {
    class mysqli_result_wrapper {
        private $rows = [];
        private $pos = 0;
        public function __construct(array $rows) { $this->rows = $rows; }
        public function fetch_assoc() { if (!isset($this->rows[$this->pos])) return null; return $this->rows[$this->pos++]; }
        public function fetch_array() { $r=$this->fetch_assoc(); if ($r===null) return null; $a=array_values($r); return array_merge($a,$r); }
        public function num_rows() { return count($this->rows); }
        public function fetch_all() { return $this->rows; }
    }
}

if (!class_exists('mysqli')) {
    class mysqli {
        private $pdo;
        public function __construct($host=null,$user=null,$pass=null,$db=null) { $this->pdo = Db::getConnection(); }
        public function query($sql) {
            $trim = ltrim($sql);
            $isSelect = stripos($trim,'select')===0 || stripos($trim,'with')===0;
            if ($isSelect) { $stmt=$this->pdo->query($sql); return new mysqli_result_wrapper($stmt->fetchAll()); }
            $stmt=$this->pdo->prepare($sql); return $stmt->execute();
        }
        public function real_escape_string($s){ $q=$this->pdo->quote($s); return $q===false ? '' : substr($q,1,-1); }
        public function prepare($sql){ return $this->pdo->prepare($sql); }
        public function close(){ $this->pdo=null; }
    }
}
