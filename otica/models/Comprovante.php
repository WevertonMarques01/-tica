<?php
class Comprovante extends BaseModel
{
    protected $table = 'comprovantes_pagamento';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getByCliente($clienteId, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE cliente_id = ? ORDER BY criado_em DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }
    
    public function getByVenda($vendaId)
    {
        return $this->find(['venda_id' => $vendaId]);
    }
    
    public function search($termo)
    {
        $sql = "SELECT c.*, cli.nome as cliente_nome 
                FROM {$this->table} c
                LEFT JOIN clientes cli ON c.cliente_id = cli.id
                WHERE c.nome_original LIKE :termo 
                   OR c.descricao LIKE :termo
                   OR cli.nome LIKE :termo
                ORDER BY c.criado_em DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':termo', '%' . $termo . '%');
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getEstatisticas($clienteId = null)
    {
        $where = $clienteId ? " WHERE cliente_id = " . (int)$clienteId : "";
        $sql = "SELECT 
                    COUNT(*) as total_comprovantes,
                    COALESCE(SUM(valor_pagamento), 0) as valor_total,
                    COALESCE(AVG(valor_pagamento), 0) as valor_medio
                FROM {$this->table}" . $where;
        return $this->db->query($sql)->fetch();
    }
}