<?php
class DashboardModel {
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }

    public function getVendasHoje(): array {
        $sql = "SELECT COUNT(*) AS qtd, COALESCE(SUM(v.total_geral),0) AS total
                FROM vendas v
                WHERE v.status IN ('finalizada','concluida','paga')
                  AND v.created_at >= CURRENT_DATE()
                  AND v.created_at <  CURRENT_DATE() + INTERVAL 1 DAY";
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC) ?: ['qtd'=>0,'total'=>0];
    }

    public function getNovosClientesHoje(): int {
        $sql = "SELECT COUNT(*) AS qtd
                FROM clientes c
                WHERE c.created_at >= CURRENT_DATE()
                  AND c.created_at <  CURRENT_DATE() + INTERVAL 1 DAY";
        return (int)($this->db->query($sql)->fetchColumn() ?: 0);
    }

    public function getEstoque(): array {
        $sql = "SELECT
                    COALESCE(SUM(p.estoque_atual > 0),0) AS skus,
                    COALESCE(SUM(p.estoque_atual),0)     AS pecas
                FROM produtos p";
        return $this->db->query($sql)->fetch(PDO::FETCH_ASSOC) ?: ['skus'=>0,'pecas'=>0];
    }

    public function getReceitaMensal(): float {
        $sql = "SELECT COALESCE(SUM(v.total_geral),0) AS total
                FROM vendas v
                WHERE v.status IN ('finalizada','concluida','paga')
                  AND v.created_at >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')
                  AND v.created_at <  DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01') + INTERVAL 1 MONTH";
        return (float)($this->db->query($sql)->fetchColumn() ?: 0);
    }
}
