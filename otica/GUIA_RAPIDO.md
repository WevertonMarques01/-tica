# 🚀 GUIA RÁPIDO - Sistema de Ótica

## 📋 PASSOS PARA CONFIGURAR O SISTEMA

### 1. **Verificar XAMPP**
- ✅ Abra o XAMPP Control Panel
- ✅ Inicie **Apache** e **MySQL** (clique em "Start")
- ✅ Aguarde as luzes ficarem verdes

### 2. **Criar Banco de Dados**
- Acesse: `http://localhost/phpmyadmin`
- Clique em "SQL"
- Cole o conteúdo do arquivo `otica_db_v2.sql`
- Clique em "Executar"

### 3. **Testar Conexão**
- Acesse: `http://localhost/-tica/otica/teste_conexao.php`
- Verifique se a conexão está funcionando

### 4. **Criar Administrador**
- Acesse: `http://localhost/-tica/otica/criar_admin.php`
- O script irá criar automaticamente o usuário dono

### 5. **Fazer Login**
- Acesse: `http://localhost/-tica/otica/login.php`
- Use as credenciais:
  - **Email:** `dono@otica.com`
  - **Senha:** `admin123`

---

## 🔗 URLs IMPORTANTES

| Função | URL |
|--------|-----|
| **Teste de Conexão** | `http://localhost/-tica/otica/teste_conexao.php` |
| **Criar Admin** | `http://localhost/-tica/otica/criar_admin.php` |
| **Login** | `http://localhost/-tica/otica/login.php` |
| **Painel Admin** | `http://localhost/-tica/otica/views/admin/index.php` |
| **Página Inicial** | `http://localhost/-tica/otica/index.php` |

---

## 👑 CREDENCIAIS DO DONO

- **Email:** `dono@otica.com`
- **Senha:** `admin123`
- **Perfil:** Administrador (acesso total)

---

## 🔧 FLUXO DE REDIRECIONAMENTO

1. **Login** (`login.php`) → **Painel Admin** (`views/admin/index.php`)
2. **Logout** → **Login** (`login.php`)
3. **Acesso Negado** → **Login** (`login.php`)

---

## 🚨 SOLUÇÃO DE PROBLEMAS

### **Erro de Conexão**
1. Verifique se XAMPP está rodando
2. Teste: `http://localhost/-tica/otica/teste_conexao.php`
3. Verifique as configurações em `config/database.php`

### **Banco não Existe**
1. Acesse phpMyAdmin
2. Execute o script `otica_db_v2.sql`

### **Usuário não Encontrado**
1. Acesse: `http://localhost/-tica/otica/criar_admin.php`
2. O script criará o usuário automaticamente

### **Erro de Login**
1. Verifique se o usuário está ativo
2. Verifique se a senha está correta
3. Use as credenciais padrão: `dono@otica.com` / `admin123`

---

## 📁 ESTRUTURA DE ARQUIVOS

```
-tica/otica/
├── config/
│   └── database.php          # Configurações do banco
├── controllers/
│   └── LoginController.php   # Controle de login/logout
├── includes/
│   └── auth_check.php        # Verificação de autenticação
├── views/
│   └── admin/
│       └── index.php         # Painel administrativo
├── login.php                 # Página de login
├── criar_admin.php           # Criar usuário administrador
├── teste_conexao.php         # Teste de conexão
└── otica_db_v2.sql          # Script do banco de dados
```

---

## ✅ CHECKLIST FINAL

- [ ] XAMPP rodando (Apache + MySQL)
- [ ] Banco `otica_db` criado
- [ ] Tabelas criadas (execute `otica_db_v2.sql`)
- [ ] Conexão testada (`teste_conexao.php`)
- [ ] Administrador criado (`criar_admin.php`)
- [ ] Login funcionando
- [ ] Sistema operacional

**🎉 Se todos os itens estiverem marcados, o sistema está funcionando!**
