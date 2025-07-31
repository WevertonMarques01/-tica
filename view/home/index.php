<?php include 'view/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="jumbotron bg-light p-5 rounded">
            <h1 class="display-4">
                <i class="fas fa-rocket text-primary"></i> 
                Bem-vindo à Aplicação MVC!
            </h1>
            <p class="lead"><?= isset($message) ? $message : 'Esta é uma aplicação PHP desenvolvida seguindo o padrão MVC.' ?></p>
            <hr class="my-4">
            <p>Esta estrutura MVC inclui:</p>
            <ul class="list-unstyled">
                <li><i class="fas fa-check text-success"></i> Controllers para gerenciar a lógica de negócio</li>
                <li><i class="fas fa-check text-success"></i> Models para interação com o banco de dados</li>
                <li><i class="fas fa-check text-success"></i> Views para apresentação dos dados</li>
                <li><i class="fas fa-check text-success"></i> Sistema de rotas automático</li>
                <li><i class="fas fa-check text-success"></i> Layout responsivo com Bootstrap</li>
            </ul>
            <a class="btn btn-primary btn-lg" href="/users" role="button">
                <i class="fas fa-users"></i> Gerenciar Usuários
            </a>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-code fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Estrutura MVC</h5>
                <p class="card-text">Organização clara e separação de responsabilidades seguindo o padrão Model-View-Controller.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-database fa-3x text-success mb-3"></i>
                <h5 class="card-title">Banco de Dados</h5>
                <p class="card-text">Integração com MySQL usando PDO para operações seguras e eficientes.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-mobile-alt fa-3x text-info mb-3"></i>
                <h5 class="card-title">Responsivo</h5>
                <p class="card-text">Interface moderna e responsiva que funciona em todos os dispositivos.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'view/layout/footer.php'; ?> 