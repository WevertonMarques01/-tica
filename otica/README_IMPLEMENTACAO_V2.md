# 🚀 IMPLEMENTAÇÃO DO BANCO DE DADOS V2.0
## Sistema de Ótica - Guia de Implementação

### 📋 PRÉ-REQUISITOS

- ✅ MySQL/MariaDB 5.7+ ou MySQL 8.0+
- ✅ Acesso de administrador ao banco de dados
- ✅ Backup do banco atual (otica_db)
- ✅ Sistema PHP funcionando

---

## 🔧 PASSOS PARA IMPLEMENTAÇÃO

### 1. **BACKUP DO BANCO ATUAL**

```bash
# Fazer backup completo do banco atual
mysqldump -u root -p otica_db > backup_otica_v1_$(date +%Y%m%d_%H%M%S).sql

# Verificar se o backup foi criado
ls -la backup_otica_v1_*.sql
```

### 2. **CRIAR O NOVO BANCO**

```bash
# Acessar o MySQL
mysql -u root -p

# Executar o script de criação
source /caminho/para/otica_db_v2.sql
```

**OU via phpMyAdmin:**
1. Acesse o phpMyAdmin
2. Clique em "SQL"
3. Cole o conteúdo do arquivo `otica_db_v2.sql`
4. Clique em "Executar"

### 3. **MIGRAR DADOS (OPCIONAL)**

Se você tem dados no banco antigo que deseja preservar:

```bash
# Executar o script de migração
mysql -u root -p otica_db_v2 < migracao_v1_para_v2.sql
```

### 4. **ATUALIZAR CONFIGURAÇÃO DO SISTEMA**

Edite o arquivo `config/database.php`:

```php
<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'otica_db_v2'); // ← ALTERAR AQUI
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### 5. **TESTAR O SISTEMA**

1. Acesse: `http://localhost/-tica/otica/login.php`
2. Use as credenciais:
   - **Email**: admin@otica.com
   - **Senha**: admin123
3. Teste todas as funcionalidades:
   - Cadastro de clientes
   - Cadastro de produtos
   - Criação de receitas
   - Registro de vendas
   - Ordens de serviço

---

## 🔍 VERIFICAÇÕES PÓS-IMPLEMENTAÇÃO

### 1. **Verificar Estrutura do Banco**

```sql
-- Verificar se todas as tabelas foram criadas
SHOW TABLES;

-- Verificar se os dados iniciais foram inseridos
SELECT COUNT(*) as total_usuarios FROM usuarios;
SELECT COUNT(*) as total_categorias FROM categorias_produtos;
SELECT COUNT(*) as total_marcas FROM marcas;
SELECT COUNT(*) as total_configuracoes FROM configuracoes;
```

### 2. **Testar Funcionalidades**

- ✅ Login do administrador
- ✅ Cadastro de clientes
- ✅ Cadastro de produtos
- ✅ Criação de receitas
- ✅ Registro de vendas
- ✅ Controle de estoque
- ✅ Ordens de serviço

### 3. **Verificar Triggers**

```sql
-- Verificar se os triggers foram criados
SHOW TRIGGERS;

-- Testar geração automática de números
INSERT INTO vendas (cliente_id, usuario_id, valor_total, forma_pagamento) 
VALUES (1, 1, 100.00, 'dinheiro');
-- Verificar se o número foi gerado automaticamente
```

### 4. **Testar Procedures**

```sql
-- Testar procedure de relatório de vendas
CALL sp_relatorio_vendas_periodo('2025-01-01', '2025-12-31');

-- Testar procedure de estoque baixo
CALL sp_relatorio_estoque_baixo();

-- Testar procedure de receitas vencidas
CALL sp_relatorio_receitas_vencidas();
```

---

## 🛠️ CONFIGURAÇÕES ADICIONAIS

### 1. **Configurações do Sistema**

Acesse a tabela `configuracoes` para personalizar:

```sql
-- Ver configurações atuais
SELECT * FROM configuracoes;

-- Atualizar dados da empresa
UPDATE configuracoes SET valor = 'Nome da Sua Ótica' WHERE chave = 'empresa_nome';
UPDATE configuracoes SET valor = '00.000.000/0001-00' WHERE chave = 'empresa_cnpj';
UPDATE configuracoes SET valor = 'Rua da Sua Ótica, 123' WHERE chave = 'empresa_endereco';
UPDATE configuracoes SET valor = '(11) 99999-9999' WHERE chave = 'empresa_telefone';
UPDATE configuracoes SET valor = 'contato@suaotica.com' WHERE chave = 'empresa_email';
```

### 2. **Criar Usuários Adicionais**

```sql
-- Criar usuário vendedor
INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES 
('Vendedor', 'vendedor@otica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor', 1);

-- Criar usuário óptico
INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES 
('Óptico', 'optico@otica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'optico', 1);
```

### 3. **Adicionar Categorias e Marcas**

```sql
-- Adicionar categorias específicas
INSERT INTO categorias_produtos (nome, descricao) VALUES 
('Lentes de Contato', 'Lentes de contato descartáveis e permanentes'),
('Produtos Infantis', 'Óculos e acessórios para crianças');

-- Adicionar marcas específicas
INSERT INTO marcas (nome, descricao) VALUES 
('Johnson & Johnson', 'Fabricante de lentes de contato'),
('Bausch & Lomb', 'Fabricante de produtos oftalmológicos');
```

---

## 🔒 SEGURANÇA

### 1. **Alterar Senha do Administrador**

```sql
-- Gerar nova senha hash (use password_hash() no PHP)
UPDATE usuarios 
SET senha = '$2y$10$novo_hash_aqui' 
WHERE email = 'admin@otica.com';
```

### 2. **Configurar Backup Automático**

```bash
# Criar script de backup
cat > backup_otica.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p otica_db_v2 > backup_otica_v2_$DATE.sql
find . -name "backup_otica_v2_*.sql" -mtime +7 -delete
EOF

chmod +x backup_otica.sh

# Adicionar ao crontab (backup diário às 2h)
crontab -e
# Adicionar linha: 0 2 * * * /caminho/para/backup_otica.sh
```

### 3. **Configurar Permissões de Arquivo**

```bash
# Configurar permissões adequadas
chmod 644 config/database.php
chmod 755 -R views/
chmod 755 -R controllers/
chmod 755 -R models/
```

---

## 📊 MONITORAMENTO

### 1. **Verificar Logs do Sistema**

```sql
-- Ver logs recentes
SELECT * FROM logs_sistema ORDER BY created_at DESC LIMIT 50;

-- Ver logs por usuário
SELECT usuario_id, acao, COUNT(*) as total 
FROM logs_sistema 
GROUP BY usuario_id, acao 
ORDER BY total DESC;
```

### 2. **Monitorar Performance**

```sql
-- Verificar tabelas com mais registros
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'otica_db_v2'
ORDER BY TABLE_ROWS DESC;
```

### 3. **Verificar Integridade**

```sql
-- Verificar relacionamentos
SELECT 
    COUNT(*) as total_vendas,
    COUNT(DISTINCT cliente_id) as clientes_unicos,
    COUNT(DISTINCT usuario_id) as usuarios_unicos
FROM vendas;

-- Verificar estoque
SELECT 
    COUNT(*) as total_produtos,
    SUM(CASE WHEN estoque_atual <= estoque_minimo THEN 1 ELSE 0 END) as produtos_estoque_baixo
FROM produtos WHERE ativo = 1;
```

---

## 🚨 SOLUÇÃO DE PROBLEMAS

### 1. **Erro de Conexão**

```php
// Verificar configuração
var_dump(DB_HOST, DB_NAME, DB_USER, DB_PASS);

// Testar conexão
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    echo "Conexão OK";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
```

### 2. **Tabelas Não Criadas**

```sql
-- Verificar se o banco existe
SHOW DATABASES LIKE 'otica_db_v2';

-- Verificar tabelas
SHOW TABLES;

-- Recriar se necessário
source otica_db_v2.sql;
```

### 3. **Dados Não Migrados**

```sql
-- Verificar dados migrados
SELECT 'usuarios' as tabela, COUNT(*) as total FROM usuarios
UNION ALL
SELECT 'clientes', COUNT(*) FROM clientes
UNION ALL
SELECT 'produtos', COUNT(*) FROM produtos;

-- Executar migração novamente se necessário
source migracao_v1_para_v2.sql;
```

### 4. **Triggers Não Funcionando**

```sql
-- Verificar triggers
SHOW TRIGGERS;

-- Recriar triggers se necessário
DELIMITER $$
CREATE TRIGGER `tr_vendas_numero_auto` 
BEFORE INSERT ON `vendas` 
FOR EACH ROW 
BEGIN
    IF NEW.numero_venda IS NULL THEN
        SET NEW.numero_venda = CONCAT('VDA', YEAR(NOW()), LPAD(MONTH(NOW()), 2, '0'), LPAD((SELECT COUNT(*) + 1 FROM vendas WHERE YEAR(data_venda) = YEAR(NOW()) AND MONTH(data_venda) = MONTH(NOW())), 4, '0'));
    END IF;
END$$
DELIMITER ;
```

---

## 📞 SUPORTE

### Em Caso de Problemas:

1. **Verifique os logs do sistema**
2. **Consulte a documentação completa** (`DOCUMENTACAO_BANCO_V2.md`)
3. **Faça backup antes de qualquer alteração**
4. **Teste em ambiente de desenvolvimento primeiro**

### Contatos para Suporte:
- **Documentação**: `DOCUMENTACAO_BANCO_V2.md`
- **Script de Migração**: `migracao_v1_para_v2.sql`
- **Banco de Dados**: `otica_db_v2.sql`

---

## ✅ CHECKLIST FINAL

- [ ] Backup do banco antigo realizado
- [ ] Novo banco criado com sucesso
- [ ] Dados migrados (se aplicável)
- [ ] Configuração atualizada
- [ ] Login funcionando
- [ ] Todas as funcionalidades testadas
- [ ] Usuários adicionais criados
- [ ] Configurações personalizadas
- [ ] Backup automático configurado
- [ ] Monitoramento ativo

**🎉 Parabéns! O sistema está pronto para uso em produção!**
