<?php
/**
 * Arquivo de Compatibilidade - Banco de Dados
 * Resolve diferenças entre estrutura do banco e código
 */

class DatabaseCompatibility {
    public static function getUserFields() {
        static $fields = null;
        
        if ($fields === null) {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->query("DESCRIBE usuarios");
                $columns = $stmt->fetchAll();
                
                $fields = [
                    "senha" => "senha",
                    "perfil" => "perfil"
                ];
                
                foreach ($columns as $column) {
                    if ($column["Field"] === "senha_hash") {
                        $fields["senha"] = "senha_hash";
                    }
                    if ($column["Field"] === "permissao") {
                        $fields["perfil"] = "permissao";
                    }
                }
            } catch (Exception $e) {
                // Fallback para estrutura padrão
                $fields = [
                    "senha" => "senha",
                    "perfil" => "perfil"
                ];
            }
        }
        
        return $fields;
    }
    
    public static function buildUserQuery($email) {
        $fields = self::getUserFields();
        $senha_field = $fields["senha"];
        $perfil_field = $fields["perfil"];
        
        return "SELECT id, nome, email, {$senha_field} as senha, {$perfil_field} as perfil FROM usuarios WHERE email = ?";
    }
}
?>