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

- **Host**: localhost
- **Database**: otica_db
- **User**: root
- **Password**: (vazio)
- **Charset**: utf8mb4

## Teste de Conexão

Para testar se a conexão está funcionando, acesse:
```
http://localhost/-tica/otica/test_connection.php
```

## Requisitos

- XAMPP instalado e rodando
- MySQL ativo
- Banco de dados `otica_db` criado
- PHP com extensão PDO habilitada

## Solução de Problemas

1. **Erro de conexão**: Verifique se o MySQL está rodando no XAMPP
2. **Banco não encontrado**: Importe o arquivo `otica_db.sql` no phpMyAdmin
3. **Credenciais incorretas**: Verifique as configurações no arquivo `database.php` 