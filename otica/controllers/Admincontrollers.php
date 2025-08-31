<?php
require_once __DIR__ . '/../models/admin.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../includes/Database.php';
class DashboardController {
    public function index() {
        // garanta o fuso para evitar “dia errado”
        date_default_timezone_set('America/Sao_Paulo');

        $model = new DashboardModel(Database::getInstance()); // seu singleton/conexão
        $vendasHoje   = $model->getVendasHoje();
        $clientesHoje = $model->getNovosClientesHoje();
        $estoque      = $model->getEstoque();
        $receitaMes   = $model->getReceitaMensal();

        require __DIR__ . '/../admin/index.php';
    }
}
