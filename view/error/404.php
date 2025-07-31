<?php include 'view/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12 text-center">
        <div class="error-page">
            <h1 class="display-1 text-muted">404</h1>
            <h2 class="mb-4">Página não encontrada</h2>
            <p class="lead mb-4">A página que você está procurando não existe ou foi movida.</p>
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Voltar ao Início
            </a>
        </div>
    </div>
</div>

<style>
.error-page {
    padding: 4rem 0;
}
.error-page .display-1 {
    font-size: 8rem;
    font-weight: bold;
    color: #6c757d;
}
</style>

<?php include 'view/layout/footer.php'; ?> 