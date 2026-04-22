# Configuração do Sistema Ótica

## Estrutura da Pasta Config

```
config/
├── database.php    # Configuração do banco de dados
├── config.php      # Configurações gerais do sistema
└── README.md       # Este arquivo
```

## Como Usar a Conexão

### 1. Incluir o arquivo de configuração
```php
require_once 'config/database.php';
```

### 2. Obter a conexão
```php
$db = Database::getInstance();
$connection = $db->getConnection();
```

### 3. Exemplo de uso
```php
try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    
    // Executar uma consulta
    $stmt = $connection->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
```

## Configurações do Banco de Dados

As configurações estão no arquivo `database.php`:

- **Host**: localhost (geralmente no InfinityFree)
- **Database**: otica_db (nome do seu banco)
- **User**: root (ou usuário fornecido)
- **Password**: (definido no painel do InfinityFree)
- **Charset**: utf8mb4

## Requisitos

- PHP 7.4 ou superior (recomendado 8.0+)
- Extensão PDO e PDO_MySQL habilitadas
- MySQL/MariaDB
- Servidor Apache com mod_rewrite (opcional)

## Solução de Problemas

1. **Erro de conexão**: Verifique se as credenciais em `database.php` estão corretas e se o banco foi importado.
2. **Banco não encontrado**: Importe o arquivo `otica_db.sql` no phpMyAdmin.
3. **Upload falha**: Verifique permissões da pasta `uploads/` (755 ou 777).
4. **CSS/JS não carrega**: Use o inspetor do navegador (F12) para verificar erros 404.