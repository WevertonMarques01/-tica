<?php include 'view/layout/header.php'; ?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-plus"></i> Criar Novo Usuário</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/users/create">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= isset($user['name']) ? htmlspecialchars($user['name']) : '' ?>"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha *</label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               required>
                        <div class="form-text">A senha deve ter pelo menos 6 caracteres.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Senha *</label>
                        <input type="password" 
                               class="form-control" 
                               id="confirm_password" 
                               name="confirm_password" 
                               required>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/users" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Criar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validação de senha
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('As senhas não coincidem');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php include 'view/layout/footer.php'; ?> 