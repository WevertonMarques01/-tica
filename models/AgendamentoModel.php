<?php
/**
 * Model Agendamento - Gerenciamento de agendamentos
 */
require_once __DIR__ . '/BaseModel.php';

class AgendamentoModel extends BaseModel
{
    protected $table = 'agendamentos';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getAllByDateRange($dataInicio, $dataFim)
    {
        $sql = "SELECT a.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                FROM {$this->table} a
                LEFT JOIN clientes c ON a.cliente_id = c.id
                WHERE DATE(a.data_consulta) BETWEEN :data_inicio AND :data_fim
                ORDER BY a.data_consulta ASC, a.hora_consulta ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data_inicio', $dataInicio);
        $stmt->bindValue(':data_fim', $dataFim);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id)
    {
        $sql = "SELECT a.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                FROM {$this->table} a
                LEFT JOIN clientes c ON a.cliente_id = c.id
                WHERE a.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function create($data)
    {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $fieldList = implode(', ', $fields);
        
        $sql = "INSERT INTO {$this->table} ({$fieldList}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($data);
    }
    
    public function update($data)
    {
        if (!isset($data['id'])) {
            return false;
        }
        
        $id = $data['id'];
        unset($data['id']);
        
        $fields = array_keys($data);
        $setClauses = [];
        foreach ($fields as $field) {
            $setClauses[] = "{$field} = :{$field}";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE id = :id";
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function verificarConflito($clienteId, $dataConsulta, $horaConsulta, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE cliente_id = :cliente_id 
                AND data_consulta = :data_consulta 
                AND hora_consulta = :hora_consulta";
        
        $params = [
            ':cliente_id' => $clienteId,
            ':data_consulta' => $dataConsulta,
            ':hora_consulta' => $horaConsulta
        ];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    public function getAgendamentosPorStatus($status, $dataInicio = null, $dataFim = null)
    {
        $sql = "SELECT a.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                FROM {$this->table} a
                LEFT JOIN clientes c ON a.cliente_id = c.id
                WHERE a.status = :status";
        
        $params = [':status' => $status];
        
        if ($dataInicio && $dataFim) {
            $sql .= " AND DATE(a.data_consulta) BETWEEN :data_inicio AND :data_fim";
            $params[':data_inicio'] = $dataInicio;
            $params[':data_fim'] = $dataFim;
        }
        
        $sql .= " ORDER BY a.data_consulta ASC, a.hora_consulta ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}