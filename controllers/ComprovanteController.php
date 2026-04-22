<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

class ComprovanteController
{
    private $db;
    private $uploadDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    private $maxFileSize = 10485760; // 10MB

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->uploadDir = __DIR__ . '/../../uploads/comprovantes/';
    }
    
    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Método não permitido'];
        }
        
        $clienteId = $_POST['cliente_id'] ?? null;
        $vendaId = $_POST['venda_id'] ?? null;
        $valorPagamento = $_POST['valor_pagamento'] ?? null;
        $descricao = $_POST['descricao'] ?? '';
        
        if (!$clienteId) {
            return ['success' => false, 'message' => 'Cliente é obrigatório'];
        }
        
        if (!isset($_FILES['comprovante']) || $_FILES['comprovante']['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Arquivo é obrigatório'];
        }
        
        $file = $_FILES['comprovante'];
        
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'message' => 'Arquivo muito grande (máx 10MB)'];
        }
        
        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('comp_') . '_' . time() . '.' . $extension;
        $targetPath = $this->uploadDir . $newFilename;
        
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Erro ao salvar arquivo'];
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO comprovantes_pagamento 
                (cliente_id, venda_id, nome_arquivo, nome_original, tipo_arquivo, tamanho_arquivo, valor_pagamento, descricao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $clienteId,
                $vendaId ?: null,
                $newFilename,
                $file['name'],
                $file['type'],
                $file['size'],
                $valorPagamento ? str_replace(',', '.', $valorPagamento) : null,
                $descricao
            ]);
            
            return [
                'success' => true,
                'message' => 'Comprovante salvo com sucesso',
                'id' => $this->db->lastInsertId()
            ];
        } catch (PDOException $e) {
            @unlink($targetPath);
            return ['success' => false, 'message' => 'Erro ao salvar no banco: ' . $e->getMessage()];
        }
    }
    
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM comprovantes_pagamento WHERE id = ?");
            $stmt->execute([$id]);
            $comprovante = $stmt->fetch();
            
            if (!$comprovante) {
                return ['success' => false, 'message' => 'Comprovante não encontrado'];
            }
            
            $filePath = $this->uploadDir . $comprovante['nome_arquivo'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            
            $stmt = $this->db->prepare("DELETE FROM comprovantes_pagamento WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Comprovante excluído'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()];
        }
    }
    
    public function getByCliente($clienteId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM comprovantes_pagamento 
            WHERE cliente_id = ? 
            ORDER BY criado_em DESC
        ");
        $stmt->execute([$clienteId]);
        return $stmt->fetchAll();
    }
    
    public function getAll($limit = 100, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, cli.nome as cliente_nome, v.total as venda_total
            FROM comprovantes_pagamento c
            LEFT JOIN clientes cli ON c.cliente_id = cli.id
            LEFT JOIN vendas v ON c.venda_id = v.id
            ORDER BY c.criado_em DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
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
                FROM comprovantes_pagamento" . $where;
        return $this->db->query($sql)->fetch();
    }
    
    public function getVendas($clienteId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, total, data_venda, forma_pagamento 
                FROM vendas 
                WHERE cliente_id = ?
                ORDER BY data_venda DESC
            ");
            $stmt->execute([$clienteId]);
            $vendas = $stmt->fetchAll();
            error_log("Vendas encontradas para cliente $clienteId: " . count($vendas));
            return $vendas;
        } catch (PDOException $e) {
            error_log("Erro getVendas: " . $e->getMessage());
            return [];
        }
    }
}