<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Nova Venda';
$moduleName = 'Registre uma nova venda';

$erro = '';
$success = false;

try {
    $stmt = $db->query("SELECT id, nome, cpf FROM clientes ORDER BY nome");
    $clientes = $stmt->fetchAll();
    
    $stmt = $db->query("SELECT id, nome, preco FROM produtos WHERE estoque > 0 ORDER BY nome");
    $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao carregar dados: ' . $e->getMessage();
    $clientes = [];
    $produtos = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? null;
    $forma_pagamento = $_POST['forma_pagamento'] ?? '';
    $valor_total = (float)($_POST['valor_total'] ?? 0);
    $produtos_venda = $_POST['produtos'] ?? [];
    
    if (empty($forma_pagamento) || $valor_total <= 0) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        try {
            $db->beginTransaction();
            
            $stmt = $db->prepare("INSERT INTO vendas (cliente_id, usuario_id, total, forma_pagamento, data_venda) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$cliente_id, $_SESSION['usuario_id'], $valor_total, $forma_pagamento]);
            
            $venda_id = $db->lastInsertId();
            
            foreach ($produtos_venda as $p) {
                if (!empty($p['id']) && !empty($p['quantidade'])) {
                    $stmt = $db->prepare("SELECT preco FROM produtos WHERE id = ?");
                    $stmt->execute([$p['id']]);
                    $prod = $stmt->fetch();
                    
                    if ($prod) {
                        $subtotal = $prod['preco'] * $p['quantidade'];
                        $stmt = $db->prepare("INSERT INTO venda_produtos (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$venda_id, $p['id'], $p['quantidade'], $prod['preco']]);
                    }
                }
            }
            
            $db->commit();
            $success = true;
            header('Location: historico.php?success=1');
            exit;
            
        } catch (PDOException $e) {
            $db->rollBack();
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

include '../layout_base.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .produto-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 10px;
    }
    
    .produto-row .form-group {
        margin-bottom: 0;
    }
    
    .resumo-venda {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 16px;
    }
    
    .resumo-venda .label {
        color: rgba(255,255,255,0.7);
        font-size: 0.875rem;
    }
    
    .resumo-venda .value {
        font-size: 1.5rem;
        font-weight: 700;
    }
</style>

<div class="card">
    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        Venda realizada com sucesso!
    </div>
    <?php endif; ?>

    <?php if ($erro): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?php echo htmlspecialchars($erro); ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="vendaForm" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulário -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card" style="margin-bottom: 0;">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i>
                        Dados da Venda
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-select">
                                <option value="">Selecione um cliente (opcional)</option>
                                <?php foreach ($clientes as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="forma_pagamento" class="form-label">Forma de Pagamento *</label>
                            <select name="forma_pagamento" id="forma_pagamento" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                                <option value="cartao_debito">Cartão de Débito</option>
                                <option value="pix">PIX</option>
                                <option value="boleto">Boleto</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="card" style="margin-bottom: 0;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="card-title">
                            <i class="fas fa-box"></i>
                            Produtos
                        </h3>
                        <button type="button" onclick="addProduto()" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Adicionar Produto
                        </button>
                    </div>
                    
                    <div id="produtos-container">
                        <div class="produto-row">
                            <div class="form-group">
                                <label class="form-label">Produto</label>
                                <select name="produtos[0][id]" class="form-select produto-select">
                                    <option value="">Selecione</option>
                                    <?php foreach ($produtos as $p): ?>
                                    <option value="<?php echo $p['id']; ?>" data-preco="<?php echo $p['preco']; ?>">
                                        <?php echo htmlspecialchars($p['nome']); ?> - R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Quantidade</label>
                                <input type="number" name="produtos[0][quantidade]" class="form-input produto-qtd" value="1" min="1">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-input produto-subtotal" value="R$ 0,00" readonly>
                            </div>
                            <button type="button" onclick="removeProduto(this)" class="btn-icon danger" title="Remover">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumo -->
            <div>
                <div class="resumo-venda">
                    <div class="mb-4">
                        <div class="label">Total de Itens</div>
                        <div class="value" id="total-itens">0</div>
                    </div>
                    <div class="mb-4">
                        <div class="label">Valor Total</div>
                        <div class="value" id="valor-total">R$ 0,00</div>
                    </div>
                    <input type="hidden" name="valor_total" id="valor_total" value="0">
                </div>
                
                <div class="flex flex-col gap-3 mt-4">
                    <button type="submit" class="btn btn-primary w-full py-3">
                        <i class="fas fa-check"></i>
                        Finalizar Venda
                    </button>
                    <a href="historico.php" class="btn btn-secondary w-full">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let produtoIndex = 1;

function addProduto() {
    const container = document.getElementById('produtos-container');
    const html = `
        <div class="produto-row">
            <div class="form-group">
                <label class="form-label">Produto</label>
                <select name="produtos[${produtoIndex}][id]" class="form-select produto-select">
                    <option value="">Selecione</option>
                    <?php foreach ($produtos as $p): ?>
                    <option value="<?php echo $p['id']; ?>" data-preco="<?php echo $p['preco']; ?>">
                        <?php echo htmlspecialchars($p['nome']); ?> - R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Quantidade</label>
                <input type="number" name="produtos[${produtoIndex}][quantidade]" class="form-input produto-qtd" value="1" min="1">
            </div>
            <div class="form-group">
                <label class="form-label">Subtotal</label>
                <input type="text" class="form-input produto-subtotal" value="R$ 0,00" readonly>
            </div>
            <button type="button" onclick="removeProduto(this)" class="btn-icon danger" title="Remover">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    produtoIndex++;
    attachListeners();
}

function removeProduto(btn) {
    const rows = document.querySelectorAll('.produto-row');
    if (rows.length > 1) {
        btn.closest('.produto-row').remove();
        calcularTotal();
    }
}

function attachListeners() {
    document.querySelectorAll('.produto-select').forEach(select => {
        select.addEventListener('change', function() {
            const row = this.closest('.produto-row');
            const qtdInput = row.querySelector('.produto-qtd');
            const subtotalInput = row.querySelector('.produto-subtotal');
            const option = this.options[this.selectedIndex];
            const preco = parseFloat(option.dataset.preco) || 0;
            const qtd = parseInt(qtdInput.value) || 0;
            
            subtotalInput.value = 'R$ ' + (preco * qtd).toFixed(2).replace('.', ',');
            calcularTotal();
        });
    });
    
    document.querySelectorAll('.produto-qtd').forEach(input => {
        input.addEventListener('input', function() {
            const row = this.closest('.produto-row');
            const select = row.querySelector('.produto-select');
            const option = select.options[select.selectedIndex];
            const preco = parseFloat(option.dataset.preco) || 0;
            const qtd = parseInt(this.value) || 0;
            const subtotalInput = row.querySelector('.produto-subtotal');
            
            subtotalInput.value = 'R$ ' + (preco * qtd).toFixed(2).replace('.', ',');
            calcularTotal();
        });
    });
}

function calcularTotal() {
    let total = 0;
    let itens = 0;
    
    document.querySelectorAll('.produto-row').forEach(row => {
        const select = row.querySelector('.produto-select');
        const option = select.options[select.selectedIndex];
        const preco = parseFloat(option.dataset.preco) || 0;
        const qtd = parseInt(row.querySelector('.produto-qtd').value) || 0;
        
        total += preco * qtd;
        itens += qtd;
    });
    
    document.getElementById('total-itens').textContent = itens;
    document.getElementById('valor-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
    document.getElementById('valor_total').value = total.toFixed(2);
}

attachListeners();
</script>

<?php include '../layout_end.php'; ?>
