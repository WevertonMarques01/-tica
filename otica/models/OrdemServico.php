<?php
/**
 * Model OrdemServico - Gerenciamento de ordens de serviço
 */
class OrdemServico extends BaseModel
{
    protected $table = 'ordens_servico';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Cria uma nova ordem de serviço
     */
    public function create($data)
    {
        // Adicionar data de criação
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }
    
    /**
     * Atualiza uma ordem de serviço
     */
    public function update($data)
    {
        // Adicionar data de atualização
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return parent::update($data);
    }
    
    /**
     * Busca ordens por cliente
     */
    public function getByCliente($clienteId)
    {
        $sql = "SELECT os.*, c.nome as cliente_nome FROM {$this->table} os 
                LEFT JOIN clientes c ON os.cliente_id = c.id 
                WHERE os.cliente_id = :cliente_id 
                ORDER BY os.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Busca ordens por status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT os.*, c.nome as cliente_nome FROM {$this->table} os 
                LEFT JOIN clientes c ON os.cliente_id = c.id 
                WHERE os.status = :status 
                ORDER BY os.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Busca ordens pendentes
     */
    public function getPendentes()
    {
        return $this->getByStatus('pendente');
    }
    
    /**
     * Busca ordens em andamento
     */
    public function getEmAndamento()
    {
        return $this->getByStatus('em_andamento');
    }
    
    /**
     * Busca ordens concluídas
     */
    public function getConcluidas()
    {
        return $this->getByStatus('concluida');
    }
    
    /**
     * Busca ordem com detalhes
     */
    public function getWithDetails($id)
    {
        $sql = "SELECT os.*, c.nome as cliente_nome, c.documento as cliente_documento 
                FROM {$this->table} os 
                LEFT JOIN clientes c ON os.cliente_id = c.id 
                WHERE os.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Busca todas as ordens com detalhes do cliente
     */
    public function getAllWithDetails()
    {
        $sql = "SELECT os.*, c.nome as cliente_nome FROM {$this->table} os 
                LEFT JOIN clientes c ON os.cliente_id = c.id 
                ORDER BY os.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Atualiza status da ordem
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Valida dados da ordem de serviço
     */
    public function validate($data)
    {
        $errors = [];
        
        // Validar cliente
        if (empty($data['cliente_id'])) {
            $errors['cliente_id'] = 'Cliente é obrigatório';
        }
        
        // Validar descrição
        if (empty($data['descricao'])) {
            $errors['descricao'] = 'Descrição é obrigatória';
        }
        
        // Validar valor
        if (empty($data['valor']) || !is_numeric($data['valor'])) {
            $errors['valor'] = 'Valor é obrigatório e deve ser numérico';
        }
        
        return $errors;
    }
}
?> 