<?php
/**
 * Model Produto - Gerenciamento de produtos
 */
require_once __DIR__ . '/BaseModel.php';

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
        $data['criado_em'] = date('Y-m-d H:i:s');
        
        // Buscar o ID da marca pelo nome
        $stmtMarca = $this->db->prepare("SELECT id FROM marcas WHERE nome = ?");
        $stmtMarca->execute([$data['marca']]);
        $marcaRow = $stmtMarca->fetch();
        $marca_id = $marcaRow ? $marcaRow['id'] : null;
        $data['marca_id'] = $marca_id;

        return parent::create($data);
    }
    
    /**
     * Atualiza um produto
     */
    public function update($data)
    {
        return parent::update($data);
    }
    
    /**
     * Busca produto por código
     */
    public function getByCodigo($codigo)
    {
        $result = $this->find(['codigo' => $codigo], null, 1);
        return !empty($result) ? $result[0] : null;
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
        // Use estoque_atual as per database schema, but support both field names
        $sql = "SELECT * FROM {$this->table} WHERE COALESCE(estoque_atual, estoque, 0) > 0 ORDER BY nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Atualiza estoque do produto
     */
    public function updateEstoque($id, $quantidade)
    {
        // Try estoque_atual first (primary field), fallback to estoque
        $sql = "UPDATE {$this->table} SET estoque_atual = COALESCE(estoque_atual, 0) + :quantidade WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        // If no rows affected, try the estoque field
        if (!$result || $stmt->rowCount() == 0) {
            $sql = "UPDATE {$this->table} SET estoque = COALESCE(estoque, 0) + :quantidade WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
        }
        
        return $result;
    }
    
    /**
     * Busca todos os produtos com informações completas
     */
    public function getAllWithDetails()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nome";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
        if (empty($data['preco_venda']) || !is_numeric($data['preco_venda'])) {
            $errors['preco_venda'] = 'Preço de venda é obrigatório e deve ser numérico';
        }
        
        // Validar estoque (support both estoque and estoque_atual)
        $estoqueValue = $data['estoque'] ?? $data['estoque_atual'] ?? null;
        if (!isset($estoqueValue) || !is_numeric($estoqueValue)) {
            $errors['estoque'] = 'Estoque é obrigatório e deve ser numérico';
        }
        
        return $errors;
    }
    
    /**
     * Retorna o total de estoque de todos os produtos
     */
    public function getTotalEstoque(): int {
        $sql = "SELECT COALESCE(SUM(COALESCE(estoque_atual, estoque, 0)), 0) FROM produtos";
        return (int)($this->db->query($sql)->fetchColumn() ?? 0);
    }
    
    /**
     * Retorna o total de SKUs com estoque
     */
    public function getTotalSkus(): int {
        $sql = "SELECT COUNT(*) FROM produtos WHERE COALESCE(estoque_atual, estoque, 0) > 0";
        return (int)($this->db->query($sql)->fetchColumn() ?? 0);
    }
}
?>