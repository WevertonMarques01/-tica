<?php
/**
 * Database Compatibility - WIZ ÓPTICA
 */
class DB {
    private static $columns = [];
    
    public static function getColumns($table) {
        if (!isset(self::$columns[$table])) {
            try {
                $pdo = Database::getInstance()->getConnection();
                $stmt = $pdo->query("DESCRIBE $table");
                $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
                self::$columns[$table] = array_flip($cols);
            } catch (Exception $e) {
                self::$columns[$table] = [];
            }
        }
        return self::$columns[$table];
    }
    
    public static function hasColumn($table, $column) {
        $cols = self::getColumns($table);
        return isset($cols[$column]);
    }
    
    public static function getColumn($table, $desired, $fallback) {
        if (self::hasColumn($table, $desired)) {
            return $desired;
        }
        return $fallback;
    }
    
    public static function userQuery() {
        $senha = self::getColumn('usuarios', 'senha_hash', 'senha');
        return "SELECT id, nome, email, {$senha} as senha, perfil, ativo FROM usuarios WHERE email = ?";
    }
}
