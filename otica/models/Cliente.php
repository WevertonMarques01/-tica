<?php
/**
 * Model Cliente - Gerenciamento de clientes
 */
class Cliente extends BaseModel
{
    protected $table = 'clientes';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getNovosClientesHoje(): int {
    $sql = "SELECT COUNT(*) AS qtd 
            FROM clientes 
            WHERE DATE(data_cadastro) = CURDATE()";
    return (int)($this->db->query($sql)->fetchColumn() ?? 0);
}

    
    /**
     * Atualiza um cliente
     */
    public function update($data)
    {
        // Adicionar data de atualização
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return parent::update($data);
    }
    
    /**
     * Busca cliente por CPF/CNPJ
     */
    public function getByDocumento($documento)
    {
        return $this->find(['documento' => $documento], null, 1);
    }
    
    /**
     * Verifica se documento já existe
     */
    public function documentoExists($documento, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE documento = :documento";
        $params = [':documento' => $documento];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * Busca clientes por nome
     */
    public function searchByNome($nome)
    {
        $sql = "SELECT * FROM {$this->table} WHERE nome LIKE :nome ORDER BY nome";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Valida dados do cliente
     */
    public function validate($data, $excludeId = null)
    {
        $errors = [];
        
        // Validar nome
        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        
        // Validar documento
        if (empty($data['documento'])) {
            $errors['documento'] = 'CPF/CNPJ é obrigatório';
        } elseif ($this->documentoExists($data['documento'], $excludeId)) {
            $errors['documento'] = 'CPF/CNPJ já existe';
        }
        
        // Validar email
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        }
        
        return $errors;
    }
}
?> 