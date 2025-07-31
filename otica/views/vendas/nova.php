<?php include 'otica/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-shopping-cart"></i> Nova Venda</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors) && !empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/vendas/nova" id="formVenda">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cliente_id" class="form-label">Cliente *</label>
                                <select class="form-control" id="cliente_id" name="cliente_id" required>
                                    <option value="">Selecione um cliente</option>
                                    <?php if (isset($clientes)): ?>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <option value="<?= $cliente['id'] ?>" 
                                                    <?= (isset($venda['cliente_id']) && $venda['cliente_id'] == $cliente['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cliente['nome']) ?> - <?= htmlspecialchars($cliente['documento']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="forma_pagamento" class="form-label">Forma de Pagamento *</label>
                                <select class="form-control" id="forma_pagamento" name="forma_pagamento" required>
                                    <option value="">Selecione</option>
                                    <option value="dinheiro" <?= (isset($venda['forma_pagamento']) && $venda['forma_pagamento'] == 'dinheiro') ? 'selected' : '' ?>>Dinheiro</option>
                                    <option value="cartao_credito" <?= (isset($venda['forma_pagamento']) && $venda['forma_pagamento'] == 'cartao_credito') ? 'selected' : '' ?>>Cartão de Crédito</option>
                                    <option value="cartao_debito" <?= (isset($venda['forma_pagamento']) && $venda['forma_pagamento'] == 'cartao_debito') ? 'selected' : '' ?>>Cartão de Débito</option>
                                    <option value="pix" <?= (isset($venda['forma_pagamento']) && $venda['forma_pagamento'] == 'pix') ? 'selected' : '' ?>>PIX</option>
                                    <option value="boleto" <?= (isset($venda['forma_pagamento']) && $venda['forma_pagamento'] == 'boleto') ? 'selected' : '' ?>>Boleto</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h5><i class="fas fa-box"></i> Produtos</h5>
                            <div id="produtos-container">
                                <div class="row produto-item mb-3">
                                    <div class="col-md-4">
                                        <select class="form-control produto-select" name="produtos[0][id]" required>
                                            <option value="">Selecione um produto</option>
                                            <?php if (isset($produtos)): ?>
                                                <?php foreach ($produtos as $produto): ?>
                                                    <option value="<?= $produto['id'] ?>" 
                                                            data-preco="<?= $produto['preco'] ?>"
                                                            data-estoque="<?= $produto['estoque'] ?>">
                                                        <?= htmlspecialchars($produto['nome']) ?> - R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control quantidade" name="produtos[0][quantidade]" 
                                               placeholder="Qtd" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control preco-unitario" 
                                               placeholder="Preço Unit." readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control subtotal" 
                                               placeholder="Subtotal" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-remover-produto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-success mb-3" id="adicionar-produto">
                                <i class="fas fa-plus"></i> Adicionar Produto
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?= isset($venda['observacoes']) ? htmlspecialchars($venda['observacoes']) : '' ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Resumo da Venda</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Total de Itens:</strong>
                                            <span id="total-itens">0</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Valor Total:</strong>
                                            <span id="valor-total">R$ 0,00</span>
                                        </div>
                                    </div>
                                    <input type="hidden" id="valor_total" name="valor_total" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/vendas" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Finalizar Venda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let produtoIndex = 1;

// Adicionar produto
document.getElementById('adicionar-produto').addEventListener('click', function() {
    const container = document.getElementById('produtos-container');
    const novoProduto = document.querySelector('.produto-item').cloneNode(true);
    
    // Limpar valores
    novoProduto.querySelector('.produto-select').value = '';
    novoProduto.querySelector('.quantidade').value = '1';
    novoProduto.querySelector('.preco-unitario').value = '';
    novoProduto.querySelector('.subtotal').value = '';
    
    // Atualizar nomes dos campos
    novoProduto.querySelector('.produto-select').name = `produtos[${produtoIndex}][id]`;
    novoProduto.querySelector('.quantidade').name = `produtos[${produtoIndex}][quantidade]`;
    
    container.appendChild(novoProduto);
    produtoIndex++;
    
    // Adicionar event listeners
    adicionarEventListeners(novoProduto);
});

// Remover produto
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remover-produto')) {
        if (document.querySelectorAll('.produto-item').length > 1) {
            e.target.closest('.produto-item').remove();
            calcularTotal();
        }
    }
});

// Adicionar event listeners para um item de produto
function adicionarEventListeners(produtoItem) {
    const produtoSelect = produtoItem.querySelector('.produto-select');
    const quantidadeInput = produtoItem.querySelector('.quantidade');
    const precoUnitarioInput = produtoItem.querySelector('.preco-unitario');
    const subtotalInput = produtoItem.querySelector('.subtotal');
    
    produtoSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const preco = option.dataset.preco || 0;
        precoUnitarioInput.value = `R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}`;
        calcularSubtotal(produtoItem);
    });
    
    quantidadeInput.addEventListener('input', function() {
        calcularSubtotal(produtoItem);
    });
}

// Calcular subtotal de um item
function calcularSubtotal(produtoItem) {
    const produtoSelect = produtoItem.querySelector('.produto-select');
    const quantidadeInput = produtoItem.querySelector('.quantidade');
    const subtotalInput = produtoItem.querySelector('.subtotal');
    
    const option = produtoSelect.options[produtoSelect.selectedIndex];
    const preco = parseFloat(option.dataset.preco || 0);
    const quantidade = parseInt(quantidadeInput.value || 0);
    
    const subtotal = preco * quantidade;
    subtotalInput.value = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    
    calcularTotal();
}

// Calcular total da venda
function calcularTotal() {
    let total = 0;
    let totalItens = 0;
    
    document.querySelectorAll('.produto-item').forEach(function(item) {
        const subtotalText = item.querySelector('.subtotal').value;
        if (subtotalText) {
            const subtotal = parseFloat(subtotalText.replace('R$ ', '').replace(',', '.'));
            total += subtotal;
        }
        
        const quantidade = parseInt(item.querySelector('.quantidade').value || 0);
        totalItens += quantidade;
    });
    
    document.getElementById('total-itens').textContent = totalItens;
    document.getElementById('valor-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    document.getElementById('valor_total').value = total;
}

// Adicionar event listeners para o primeiro item
document.querySelectorAll('.produto-item').forEach(function(item) {
    adicionarEventListeners(item);
});
</script>

<?php include 'otica/views/layout/footer.php'; ?> 