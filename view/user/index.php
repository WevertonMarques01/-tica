<?php include 'view/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users"></i> Gerenciar Usuários</h2>
            <a href="/users/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Usuário
            </a>
        </div>
        
        <?php if (isset($users) && !empty($users)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/users/edit?id=<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/users/delete?id=<?= $user['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nenhum usuário encontrado. 
                <a href="/users/create" class="alert-link">Criar primeiro usuário</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'view/layout/footer.php'; ?> 