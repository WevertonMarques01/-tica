<?php
/**
 * Model Venda - Gerenciamento de vendas
 */
class Venda extends BaseModel
{
    protected $table = 'vendas';
    
    public function __construct()<?php
// Supondo que $pdo seja sua conexão PDO
// Dados da venda vindos do formulário
$clienteId      = $_POST['cliente_id'];
$formaPagamento = $_POST['forma_pagamento'];
$itens          = $_POST['itens']; 
// Exemplo de $itens: [['produto_id'=>1,'qtd'=>2,'preco'=>10.50], ['produto_id'=>2,'qtd'=>1,'preco'=>5.00]]

// 1. Calcula o total geral
$totalGeral = 0;
foreach ($itens as $item) {
    $totalGeral += $item['qtd'] * $item['preco'];
}

try {
    $pdo->beginTransaction();

    // 2. Insere a venda
    $stmt = $pdo->prepare("INSERT INTO vendas (cliente_id, forma_pagamento, total_geral, created_at) 
                           VALUES (:cliente_id, :forma_pagamento, :total_geral, NOW())");
    $stmt->execute([
        ':cliente_id'     => $clienteId,
        ':forma_pagamento'=> $formaPagamento,
        ':total_geral'    => $totalGeral
    ]);

    // Pega o ID da venda inserida
    $vendaId = $pdo->lastInsertId();

    // 3. Insere os itens da venda
    $stmtItem = $pdo->prepare("INSERT INTO itens_venda (venda_id, produto_id, qtd, preco) 
                               VALUES (:venda_id, :produto_id, :qtd, :preco)");

    foreach ($itens as $item) {
        $stmtItem->execute([
            ':venda_id'   => $vendaId,
            ':produto_id' => $item['produto_id'],
            ':qtd'        => $item['qtd'],
            ':preco'      => $item['preco']
        ]);
    }

    $pdo->commit();
    echo "Venda registrada com sucesso!";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erro ao registrar venda: " . $e->getMessage();
}


    {
        parent::__construct();
    }
    
    /**
     * Cria uma nova venda
     */
    public function create($data)
    {
        // Adicionar data de criação
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }
    
    /**
     * Atualiza uma venda
     */
    public function update($data)
    {
        // Adicionar data de atualização
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return parent::update($data);
    }
    
    /**
     * Busca vendas por cliente
     */
    public function getByCliente($clienteId)
    {
        $sql = "SELECT v.*, c.nome as cliente_nome FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE v.cliente_id = :cliente_id 
                ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Busca vendas por período
     */
    public function getByPeriodo($dataInicio, $dataFim)
    {
        $sql = "SELECT v.*, c.nome as cliente_nome FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE DATE(v.created_at) BETWEEN :data_inicio AND :data_fim 
                ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data_inicio', $dataInicio);
        $stmt->bindValue(':data_fim', $dataFim);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Calcula total de vendas por período
     */
    public function getTotalByPeriodo($dataInicio, $dataFim)
    {
        $sql = "SELECT SUM(valor_total) as total FROM {$this->table} 
                WHERE DATE(created_at) BETWEEN :data_inicio AND :data_fim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':data_inicio', $dataInicio);
        $stmt->bindValue(':data_fim', $dataFim);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Busca venda com detalhes
     */
    public function getWithDetails($id)
    {
        $sql = "SELECT v.*, c.nome as cliente_nome, c.documento as cliente_documento 
                FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                WHERE v.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Busca todas as vendas com detalhes do cliente
     */
    public function getAllWithDetails()
    {
        $sql = "SELECT v.*, c.nome as cliente_nome FROM {$this->table} v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                ORDER BY v.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Valida dados da venda
     */
    public function validate($data)
    {
        $errors = [];
        
        // Validar cliente
        if (empty($data['cliente_id'])) {
            $errors['cliente_id'] = 'Cliente é obrigatório';
        }
        
        // Validar valor total
        if (empty($data['valor_total']) || !is_numeric($data['valor_total'])) {
            $errors['valor_total'] = 'Valor total é obrigatório e deve ser numérico';
        }
        
        // Validar forma de pagamento
        if (empty($data['forma_pagamento'])) {
            $errors['forma_pagamento'] = 'Forma de pagamento é obrigatória';
        }
        
        return $errors;
    }
}
?> 