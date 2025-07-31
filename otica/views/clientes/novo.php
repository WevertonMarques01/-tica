<?php include 'otica/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-plus"></i> Novo Cliente</h3>
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
                
                <form method="POST" action="/clientes/novo">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nome" 
                                       name="nome" 
                                       value="<?= isset($cliente['nome']) ? htmlspecialchars($cliente['nome']) : '' ?>"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="documento" class="form-label">CPF/CNPJ *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="documento" 
                                       name="documento" 
                                       value="<?= isset($cliente['documento']) ? htmlspecialchars($cliente['documento']) : '' ?>"
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= isset($cliente['email']) ? htmlspecialchars($cliente['email']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="telefone" 
                                       name="telefone" 
                                       value="<?= isset($cliente['telefone']) ? htmlspecialchars($cliente['telefone']) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <textarea class="form-control" 
                                  id="endereco" 
                                  name="endereco" 
                                  rows="3"><?= isset($cliente['endereco']) ? htmlspecialchars($cliente['endereco']) : '' ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/clientes" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Máscara para CPF/CNPJ
document.getElementById('documento').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 11) {
        // CPF
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    } else {
        // CNPJ
        value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    this.value = value;
});

// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }
    this.value = value;
});
</script>

<?php include 'otica/views/layout/footer.php'; ?> 