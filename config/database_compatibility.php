<?php
/**
 * Database Compatibility - WIZ ÓPTICA
 * Auto-detects column names
 */
class DatabaseCompatibility {
    private static $columns = [];
    
    public static function getColumns($table) {
        if (!isset(self::$columns[$table])) {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->query("DESCRIBE $table");
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
    
    public static function buildUserQuery($email) {
        $senha = self::hasColumn('usuarios', 'senha_hash') ? 'senha_hash' : 'senha';
        return "SELECT id, nome, email, {$senha} as senha, perfil FROM usuarios WHERE email = ?";
    }
}
