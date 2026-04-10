<?php
/**
 * Model Venda - Gerenciamento de vendas
 */
class Venda extends BaseModel
{
    protected $table = 'vendas';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function create($data)
    {
        $data['data_venda'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }
    
    public function update($data)
    {
        return parent::update($data);
    }
    
    public function getByCliente($clienteId)
    {
        $col = DB::getColumn('vendas', 'data_venda', 'created_at');
        $sql = "SELECT v.*, c.nome as cliente_nome FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE v.cliente_id = :cliente_id 
                ORDER BY v.$col DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByPeriodo($dataInicio, $dataFim)
    {
        $col = DB::getColumn('vendas', 'data_venda', 'created_at');
        $sql = "SELECT v.*, c.nome as cliente_nome FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE DATE(v.$col) BETWEEN :data_inicio AND :data_fim 
                ORDER BY v.$col DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data_inicio', $dataInicio);
        $stmt->bindValue(':data_fim', $dataFim);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getTotalByPeriodo($dataInicio, $dataFim)
    {
        $col = DB::getColumn('vendas', 'data_venda', 'created_at');
        $sql = "SELECT SUM(total) as total FROM {$this->table} 
                WHERE DATE($col) BETWEEN :data_inicio AND :data_fim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data_inicio', $dataInicio);
        $stmt->bindValue(':data_fim', $dataFim);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getWithDetails($id)
    {
        $sql = "SELECT v.*, c.nome as cliente_nome, c.cpf as cliente_documento 
                FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE v.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function getAllWithDetails()
    {
        $col = DB::getColumn('vendas', 'data_venda', 'created_at');
        $sql = "SELECT v.*, c.nome as cliente_nome, u.nome as usuario_nome 
                FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                LEFT JOIN usuarios u ON v.usuario_id = u.id 
                ORDER BY v.$col DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getTotalVendas($data = null)
    {
        $col = DB::getColumn('vendas', 'data_venda', 'created_at');
        if ($data) {
            $sql = "SELECT COALESCE(SUM(total), 0) as total FROM {$this->table} WHERE DATE($col) = :data";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':data', $data);
            $stmt->execute();
        } else {
            $sql = "SELECT COALESCE(SUM(total), 0) as total FROM {$this->table}";
            $stmt = $this->db->query($sql);
        }
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
