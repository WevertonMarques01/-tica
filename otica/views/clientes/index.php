<?php include 'otica/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users"></i> Gerenciar Clientes</h2>
            <a href="/clientes/novo" class="btn btn-primary">
                <i class="fas fa-plus"></i> Novo Cliente
            </a>
        </div>
        
        <?php if (isset($clientes) && !empty($clientes)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Documento</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= $cliente['id'] ?></td>
                                <td><?= htmlspecialchars($cliente['nome']) ?></td>
                                <td><?= htmlspecialchars($cliente['documento']) ?></td>
                                <td><?= htmlspecialchars($cliente['email']) ?></td>
                                <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($cliente['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/clientes/visualizar?id=<?= $cliente['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/clientes/editar?id=<?= $cliente['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/clientes/excluir?id=<?= $cliente['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
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
                <i class="fas fa-info-circle"></i> Nenhum cliente encontrado. 
                <a href="/clientes/novo" class="alert-link">Cadastrar primeiro cliente</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'otica/views/layout/footer.php'; ?> 