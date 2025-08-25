# 👑 CRIAR CONTA DO DONO/ADMINISTRADOR GERAL
## Sistema de Ótica - Guia Rápido

### 🎯 OBJETIVO
Criar uma conta de administrador geral (dono) para o sistema de ótica com acesso total a todas as funcionalidades.

---

## 🚀 MÉTODOS PARA CRIAR O ADMINISTRADOR

### **MÉTODO 1: Script PHP (RECOMENDADO)**

1. **Acesse o script via navegador:**
   ```
   http://localhost/-tica/otica/criar_admin.php
   ```

2. **O script irá:**
   - ✅ Verificar se o banco existe
   - ✅ Verificar se a tabela usuarios existe
   - ✅ Criar o usuário dono automaticamente
   - ✅ Mostrar todas as informações de login
   - ✅ Permitir criar outros usuários

3. **Credenciais do Dono:**
   - **Email:** dono@otica.com
   - **Senha:** admin123
   - **Perfil:** admin (acesso total)

### **MÉTODO 2: Script SQL**

1. **Execute o script SQL:**
   ```bash
   mysql -u root -p otica_db < criar_admin_dono.sql
   ```

2. **Ou via phpMyAdmin:**
   - Acesse o phpMyAdmin
   - Selecione o banco `otica_db`
   - Clique em "SQL"
   - Cole o conteúdo do arquivo `criar_admin_dono.sql`
   - Clique em "Executar"

### **MÉTODO 3: Comando SQL Direto**

```sql
USE otica_db;

-- Criar usuário dono
INSERT INTO usuarios (
    nome, 
    email, 
    senha, 
    perfil, 
    ativo, 
    created_at, 
    updated_at
) VALUES (
    'Dono da Ótica', 
    'dono@otica.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'admin', 
    1, 
    NOW(), 
    NOW()
);
```

---

## 🔑 CREDENCIAIS DE LOGIN

### **Dono da Ótica (Administrador Geral)**
- **Email:** dono@otica.com
- **Senha:** admin123
- **Perfil:** admin
- **Acesso:** Total ao sistema

### **Administrador Padrão**
- **Email:** admin@otica.com
- **Senha:** admin123
- **Perfil:** admin
- **Acesso:** Total ao sistema

### **Outros Usuários (se criados)**
- **Gerente:** gerente@otica.com / admin123
- **Vendedor:** vendedor@otica.com / admin123
- **Óptico:** optico@otica.com / admin123

---

## 📋 PERFIS DE USUÁRIO

### **admin** (Administrador)
- ✅ Acesso total ao sistema
- ✅ Gerenciar usuários
- ✅ Configurações do sistema
- ✅ Relatórios completos
- ✅ Backup e restauração

### **gerente** (Gerente)
- ✅ Gerenciar vendas e clientes
- ✅ Relatórios de vendas
- ✅ Controle de estoque
- ✅ Ordens de serviço
- ❌ Não pode gerenciar usuários

### **vendedor** (Vendedor)
- ✅ Cadastrar clientes
- ✅ Registrar vendas
- ✅ Consultar produtos
- ✅ Criar receitas
- ❌ Acesso limitado a relatórios

### **optico** (Óptico)
- ✅ Criar e editar receitas
- ✅ Consultar clientes
- ✅ Ordens de serviço
- ✅ Especificações técnicas
- ❌ Não pode fazer vendas

---

## 🔒 SEGURANÇA

### **Alterar Senha do Dono**

1. **Via PHP:**
   ```php
   $nova_senha = 'sua_nova_senha';
   $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
   
   $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = 'dono@otica.com'");
   $stmt->execute([$senha_hash]);
   ```

2. **Via SQL:**
   ```sql
   UPDATE usuarios 
   SET senha = '$2y$10$novo_hash_aqui' 
   WHERE email = 'dono@otica.com';
   ```

### **Desativar Usuário**
```sql
UPDATE usuarios SET ativo = 0 WHERE email = 'email_do_usuario';
```

### **Reativar Usuário**
```sql
UPDATE usuarios SET ativo = 1 WHERE email = 'email_do_usuario';
```

---

## 🚨 SOLUÇÃO DE PROBLEMAS

### **Erro: "Tabela usuarios não encontrada"**
- Execute primeiro o script `otica_db_v2.sql` para criar o banco
- Verifique se o banco `otica_db` existe

### **Erro: "Usuário já existe"**
- O script irá mostrar as informações do usuário existente
- Você pode atualizar a senha se necessário

### **Erro de Conexão**
- Verifique as configurações em `config/database.php`
- Confirme se o MySQL está rodando
- Verifique as credenciais do banco

### **Login não funciona**
- Verifique se a senha está correta: `admin123`
- Confirme se o usuário está ativo (`ativo = 1`)
- Verifique se o email está correto

---

## 📞 SUPORTE

### **Arquivos Importantes:**
- `criar_admin.php` - Script PHP para criar administrador
- `criar_admin_dono.sql` - Script SQL para criar administrador
- `config/database.php` - Configurações do banco
- `login.php` - Página de login do sistema

### **URLs Importantes:**
- **Criar Admin:** http://localhost/-tica/otica/criar_admin.php
- **Login:** http://localhost/-tica/otica/login.php
- **Sistema:** http://localhost/-tica/otica/

---

## ✅ CHECKLIST

- [ ] Banco de dados `otica_db` criado
- [ ] Tabela `usuarios` existe
- [ ] Usuário dono criado com sucesso
- [ ] Login funcionando
- [ ] Senha alterada (recomendado)
- [ ] Outros usuários criados (opcional)
- [ ] Sistema testado

**🎉 Pronto! O dono da ótica já pode acessar o sistema com acesso total!**
