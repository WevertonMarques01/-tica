<?php
/**
 * VendaController - Gerenciamento de vendas
 */
require_once '../config/database.php';
require_once '../config/db_compat.php';
require_once '../models/Venda.php';
require_once '../models/Cliente.php';
require_once '../models/Produto.php';
require_once '../includes/notificacao.php';

class VendaController {
    private $db;
    private $vendaModel;
    private $clienteModel;
    private $produtoModel;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->vendaModel = new Venda();
        $this->clienteModel = new Cliente();
        $this->produtoModel = new Produto();
    }
    
    public function index()
    {
        $vendas = $this->vendaModel->getAllWithDetails();
        return $vendas;
    }
    
    public function nova()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->salvar();
        }
        
        $data = [
            'clientes' => $this->clienteModel->getAll(),
            'produtos' => $this->produtoModel->getAll()
        ];
        return $data;
    }
    
    private function salvar()
    {
        try {
            $cliente_id = $_POST['cliente_id'] ?? null;
            $forma_pagamento = $_POST['forma_pagamento'] ?? '';
            $total = $_POST['total'] ?? 0;
            $itens = $_POST['itens'] ?? [];
            
            if (empty($forma_pagamento) || empty($total)) {
                Notificacao::erro('Preencha todos os campos obrigatórios');
                return null;
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO vendas (cliente_id, usuario_id, total, forma_pagamento, data_venda) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $usuario_id = $_SESSION['usuario_id'] ?? 1;
            $stmt->execute([$cliente_id, $usuario_id, $total, $forma_pagamento]);
            
            $venda_id = $this->db->lastInsertId();
            
            // Inserir itens da venda
            foreach ($itens as $item) {
                $stmtItem = $this->db->prepare("
                    INSERT INTO venda_produtos (venda_id, produto_id, quantidade, preco_unitario) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmtItem->execute([
                    $venda_id, 
                    $item['produto_id'], 
                    $item['quantidade'], 
                    $item['preco']
                ]);
            }
            
            Notificacao::sucesso('Venda registrada com sucesso!');
            return ['success' => true, 'venda_id' => $venda_id];
            
        } catch (PDOException $e) {
            error_log("Erro ao salvar venda: " . $e->getMessage());
            Notificacao::erro('Erro ao registrar venda: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function excluir($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM vendas WHERE id = ?");
            $stmt->execute([$id]);
            
            Notificacao::sucesso('Venda excluída com sucesso!');
            return ['success' => true];
            
        } catch (PDOException $e) {
            error_log("Erro ao excluir venda: " . $e->getMessage());
            Notificacao::erro('Erro ao excluir venda');
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
