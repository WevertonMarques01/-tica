<?php
/**
 * Model Produto - Gerenciamento de produtos
 */
class Produto extends BaseModel
{
    protected $table = 'produtos';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Cria um novo produto
     */
    public function create($data)
    {
        // Adicionar data de criação
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }
    
    /**
     * Atualiza um produto
     */
    public function update($data)
    {
        // Adicionar data de atualização
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return parent::update($data);
    }
    
    /**
     * Busca produto por código
     */
    public function getByCodigo($codigo)
    {
        return $this->find(['codigo' => $codigo], null, 1);
    }
    
    /**
     * Verifica se código já existe
     */
    public function codigoExists($codigo, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE codigo = :codigo";
        $params = [':codigo' => $codigo];
        
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
     * Busca produtos por nome
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
     * Busca produtos em estoque
     */
    public function getEmEstoque()
    {
        $sql = "SELECT * FROM {$this->table} WHERE estoque > 0 ORDER BY nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Atualiza estoque do produto
     */
    public function updateEstoque($id, $quantidade)
    {
        $sql = "UPDATE {$this->table} SET estoque = estoque + :quantidade WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Valida dados do produto
     */
    public function validate($data, $excludeId = null)
    {
        $errors = [];
        
        // Validar nome
        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        
        // Validar código
        if (empty($data['codigo'])) {
            $errors['codigo'] = 'Código é obrigatório';
        } elseif ($this->codigoExists($data['codigo'], $excludeId)) {
            $errors['codigo'] = 'Código já existe';
        }
        
        // Validar preço
        if (empty($data['preco']) || !is_numeric($data['preco'])) {
            $errors['preco'] = 'Preço é obrigatório e deve ser numérico';
        }
        
        // Validar estoque
        if (!isset($data['estoque']) || !is_numeric($data['estoque'])) {
            $errors['estoque'] = 'Estoque é obrigatório e deve ser numérico';
        }
        
        return $errors;
    }
}
?> 