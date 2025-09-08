<?php
/**
 * Controller Produto - Gerenciamento de produtos
 */
require_once __DIR__ . '/../models/Produto.php';

class ProdutoController
{
    private $produtoModel;
    
    public function __construct()
    {
        $this->produtoModel = new Produto();
    }
    
    /**
     * Lista todos os produtos
     */
    public function index()
    {
        $produtos = $this->produtoModel->getAllWithDetails();
        
        // Incluir a view
        include __DIR__ . '/../views/produtos/index.php';
    }
    
    /**
     * Exibe formulário para criar produto
     */
    public function novo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoData = [
                'nome' => $_POST['nome'] ?? '',
                'codigo' => $_POST['codigo'] ?? '',
                'descricao' => $_POST['descricao'] ?? '',
                'preco' => $_POST['preco'] ?? 0,
                'estoque' => $_POST['estoque'] ?? 0,
                'tipo' => $_POST['tipo'] ?? '',
                'marca' => $_POST['marca'] ?? '',
                'modelo' => $_POST['modelo'] ?? '',
                'cor' => $_POST['cor'] ?? ''
            ];
            
            // Validar dados
            $errors = $this->produtoModel->validate($produtoData);
            
            if (empty($errors)) {
                if ($this->produtoModel->create($produtoData)) {
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Erro ao criar produto';
                }
            }
        } else {
            $errors = [];
            $produtoData = [];
        }
        
        // Incluir a view
        include __DIR__ . '/../views/produtos/novo.php';
    }
    
    /**
     * Exibe formulário para editar produto
     */
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoData = [
                'id' => $id,
                'nome' => $_POST['nome'] ?? '',
                'codigo' => $_POST['codigo'] ?? '',
                'descricao' => $_POST['descricao'] ?? '',
                'preco' => $_POST['preco'] ?? 0,
                'estoque' => $_POST['estoque'] ?? 0,
                'tipo' => $_POST['tipo'] ?? '',
                'marca' => $_POST['marca'] ?? '',
                'modelo' => $_POST['modelo'] ?? '',
                'cor' => $_POST['cor'] ?? ''
            ];
            
            // Validar dados
            $errors = $this->produtoModel->validate($produtoData, $id);
            
            if (empty($errors)) {
                if ($this->produtoModel->update($produtoData)) {
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Erro ao atualizar produto';
                }
            }
        } else {
            $produtoData = $this->produtoModel->getById($id);
            $errors = [];
        }
        
        // Incluir a view
        include __DIR__ . '/../views/produtos/editar.php';
    }
    
    /**
     * Remove um produto
     */
    public function excluir()
    {
        // Verificar se foi passado um ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: produtos.php?error=id_invalido');
            exit;
        }

        $id = (int)$_GET['id'];

        // Conectar ao banco de dados
        try {
            require_once __DIR__ . '/../config/database.php';
            $db = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            error_log("Erro de conexão com banco: " . $e->getMessage());
            header('Location: produtos.php?error=erro_sistema');
            exit;
        }

        try {
            // Verificar se o produto existe
            $stmt = $db->prepare("SELECT id, nome, codigo_barras FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
            $produto = $stmt->fetch();
            
            if (!$produto) {
                error_log("Tentativa de excluir produto inexistente: ID $id");
                header('Location: produtos.php?error=produto_nao_encontrado');
                exit;
            }
            
            // Log para debug
            error_log("Iniciando exclusão do produto: ID $id - {$produto['nome']}");
            
            // Verificar se há vendas relacionadas ao produto
            $temVendasRelacionadas = false;
            try {
                $stmtVendas = $db->prepare("SELECT COUNT(*) as total FROM itens_venda WHERE produto_id = ?");
                $stmtVendas->execute([$id]);
                $vendasRelacionadas = $stmtVendas->fetch();
                
                if ($vendasRelacionadas && $vendasRelacionadas['total'] > 0) {
                    error_log("Produto ID $id tem {$vendasRelacionadas['total']} vendas associadas");
                    $temVendasRelacionadas = true;
                }
            } catch (PDOException $e) {
                // Tabela itens_venda pode não existir
                error_log("Aviso: Tabela itens_venda não encontrada: " . $e->getMessage());
            }
            
            if ($temVendasRelacionadas) {
                header('Location: produtos.php?error=produto_tem_vendas');
                exit;
            }
            
            // Verificar se há movimentações de estoque relacionadas
            $temMovimentacoesRelacionadas = false;
            try {
                $stmtMovimentacoes = $db->prepare("SELECT COUNT(*) as total FROM movimentacao_estoque WHERE produto_id = ?");
                $stmtMovimentacoes->execute([$id]);
                $movimentacoesRelacionadas = $stmtMovimentacoes->fetch();
                
                if ($movimentacoesRelacionadas && $movimentacoesRelacionadas['total'] > 0) {
                    error_log("Produto ID $id tem {$movimentacoesRelacionadas['total']} movimentações de estoque associadas");
                    $temMovimentacoesRelacionadas = true;
                }
            } catch (PDOException $e) {
                // Tabela movimentacao_estoque pode não existir
                error_log("Aviso: Tabela movimentacao_estoque não encontrada: " . $e->getMessage());
            }
            
            if ($temMovimentacoesRelacionadas) {
                header('Location: produtos.php?error=produto_tem_movimentacoes');
                exit;
            }
            
            // Iniciar transação para segurança
            $db->beginTransaction();
            
            try {
                // Excluir o produto (após verificar que não há dados relacionados)
                $stmt = $db->prepare("DELETE FROM produtos WHERE id = ?");
                $result = $stmt->execute([$id]);
                
                if (!$result) {
                    throw new Exception("Falha ao executar comando DELETE");
                }
                
                $linhasAfetadas = $stmt->rowCount();
                if ($linhasAfetadas === 0) {
                    throw new Exception("Nenhuma linha foi afetada pela exclusão");
                }
                
                error_log("Produto ID $id excluído com sucesso. Linhas afetadas: $linhasAfetadas");
                
                // Registrar log (se a tabela logs_sistema existir)
                try {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $logStmt = $db->prepare("INSERT INTO logs_sistema (usuario_id, acao, detalhes, created_at) VALUES (?, ?, ?, NOW())");
                    $logResult = $logStmt->execute([
                        $_SESSION['usuario_id'] ?? 0, 
                        'produto_excluido', 
                        "Produto ID: $id ({$produto['nome']}) excluído"
                    ]);
                    
                    if ($logResult) {
                        error_log("Log de exclusão registrado com sucesso para produto ID $id");
                    }
                } catch (PDOException $e) {
                    // Se a tabela de logs não existir, continua sem registrar
                    error_log("Aviso: Não foi possível registrar log: " . $e->getMessage());
                }
                
                // Confirmar transação
                $db->commit();
                
                header('Location: produtos.php?success=excluido');
                exit;
                
            } catch (Exception $e) {
                // Reverter transação em caso de erro
                $db->rollback();
                error_log("Erro durante exclusão do produto ID $id: " . $e->getMessage());
                header('Location: produtos.php?error=erro_exclusao');
                exit;
            }
            
        } catch (PDOException $e) {
            error_log("Erro de banco ao excluir produto ID $id: " . $e->getMessage());
            header('Location: produtos.php?error=erro_sistema');
            exit;
        } catch (Exception $e) {
            error_log("Erro geral ao excluir produto ID $id: " . $e->getMessage());
            header('Location: produtos.php?error=erro_sistema');
            exit;
        }
        exit;
    }
    
    /**
     * Busca produtos por nome (AJAX)
     */
    public function buscar()
    {
        $nome = $_GET['nome'] ?? '';
        $produtos = $this->produtoModel->searchByNome($nome);
        
        header('Content-Type: application/json');
        echo json_encode($produtos);
        exit;
    }
    
    /**
     * Verifica se código existe (AJAX)
     */
    public function verificarCodigo()
    {
        $codigo = $_GET['codigo'] ?? '';
        $excludeId = $_GET['exclude_id'] ?? null;
        
        $exists = $this->produtoModel->codigoExists($codigo, $excludeId);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
        exit;
    }
    
    /**
     * Atualiza estoque do produto
     */
    public function atualizarEstoque()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $quantidade = $_POST['quantidade'] ?? 0;
            
            if ($id && $this->produtoModel->updateEstoque($id, $quantidade)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Erro ao atualizar estoque']);
            }
            exit;
        }
    }
}
?> 