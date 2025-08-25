# 🔧 SOLUÇÃO DE PROBLEMAS
## Sistema de Ótica - Guia de Diagnóstico

### 🚨 PROBLEMA: Erro de Conexão com Banco de Dados

---

## 📋 DIAGNÓSTICO RÁPIDO

### 1. **Testar Conexão**
Acesse: `http://localhost/-tica/otica/teste_conexao.php`

Este script irá:
- ✅ Verificar se o MySQL está rodando
- ✅ Testar a conexão com o banco
- ✅ Verificar se as tabelas existem
- ✅ Mostrar usuários cadastrados
- ✅ Diagnosticar problemas

### 2. **Verificar XAMPP**
1. Abra o XAMPP Control Panel
2. Verifique se **Apache** e **MySQL** estão rodando (luz verde)
3. Se não estiverem, clique em "Start" para ambos

### 3. **Verificar Banco de Dados**
1. Acesse: `http://localhost/phpmyadmin`
2. Verifique se o banco `otica_db` existe
3. Se não existir, execute o script `otica_db_v2.sql`

---

## 🔍 PROBLEMAS COMUNS E SOLUÇÕES

### **Problema 1: "Falha ao abrir o fluxo: Nenhum arquivo ou diretório"**

**Causa:** Caminho incorreto do arquivo de configuração

**Solução:**
```php
// ❌ Errado
require_once 'config/database.php';

// ✅ Correto (se estiver no diretório -tica)
require_once 'otica/config/database.php';

// ✅ Correto (se estiver no diretório -tica/otica)
require_once 'config/database.php';
```

### **Problema 2: "Access denied for user 'root'@'localhost'"**

**Causa:** Credenciais incorretas do MySQL

**Solução:**
1. Abra o arquivo `config/database.php`
2. Verifique as configurações:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'otica_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Senha do MySQL (vazia por padrão no XAMPP)
```

3. Se você definiu senha no MySQL, atualize `DB_PASS`

### **Problema 3: "Unknown database 'otica_db'"**

**Causa:** Banco de dados não existe

**Solução:**
1. Acesse: `http://localhost/phpmyadmin`
2. Clique em "SQL"
3. Cole o conteúdo do arquivo `otica_db_v2.sql`
4. Clique em "Executar"

**OU via linha de comando:**
```bash
mysql -u root -p < otica_db_v2.sql
```

### **Problema 4: "MySQL server has gone away"**

**Causa:** Timeout de conexão

**Solução:**
1. Reinicie o MySQL no XAMPP
2. Verifique se não há outros serviços usando a porta 3306

### **Problema 5: "Can't connect to MySQL server"**

**Causa:** MySQL não está rodando

**Solução:**
1. Abra XAMPP Control Panel
2. Clique em "Start" no MySQL
3. Aguarde a luz verde
4. Teste novamente

---

## 🛠️ CONFIGURAÇÕES DO XAMPP

### **Verificar Portas**
- **Apache:** 80, 443
- **MySQL:** 3306

### **Verificar Serviços**
```bash
# Verificar se as portas estão em uso
netstat -an | findstr :80
netstat -an | findstr :3306
```

### **Reiniciar Serviços**
1. XAMPP Control Panel → Stop (Apache e MySQL)
2. Aguarde 10 segundos
3. XAMPP Control Panel → Start (Apache e MySQL)

---

## 📁 ESTRUTURA DE ARQUIVOS CORRETA

```
D:\xamppp\htdocs\-tica\
├── test_connection.php (usar: require_once 'otica/config/database.php')
└── otica\
    ├── config\
    │   └── database.php
    ├── teste_conexao.php (usar: require_once 'config/database.php')
    ├── criar_admin.php
    ├── login.php
    └── index.php
```

---

## 🔧 SCRIPTS DE DIAGNÓSTICO

### **1. Teste de Conexão Básico**
```php
<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=otica_db", "root", "");
    echo "✅ Conexão OK!";
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
```

### **2. Verificar Configurações**
```php
<?php
echo "Host: " . DB_HOST . "<br>";
echo "Banco: " . DB_NAME . "<br>";
echo "Usuário: " . DB_USER . "<br>";
echo "Senha: " . (empty(DB_PASS) ? '(vazia)' : '(definida)') . "<br>";
?>
```

### **3. Testar MySQL Direto**
```bash
mysql -u root -p
SHOW DATABASES;
USE otica_db;
SHOW TABLES;
```

---

## 📞 PASSOS PARA RESOLVER

### **Passo 1: Verificar XAMPP**
1. Abra XAMPP Control Panel
2. Verifique se Apache e MySQL estão rodando
3. Se não estiverem, clique em "Start"

### **Passo 2: Testar Conexão**
1. Acesse: `http://localhost/-tica/otica/teste_conexao.php`
2. Verifique o resultado do teste

### **Passo 3: Criar Banco (se necessário)**
1. Acesse: `http://localhost/phpmyadmin`
2. Execute o script `otica_db_v2.sql`

### **Passo 4: Criar Administrador**
1. Acesse: `http://localhost/-tica/otica/criar_admin.php`
2. Siga as instruções para criar o usuário dono

### **Passo 5: Testar Sistema**
1. Acesse: `http://localhost/-tica/otica/login.php`
2. Use as credenciais do dono

---

## 🚨 EMERGÊNCIA

### **Se nada funcionar:**

1. **Reiniciar XAMPP completamente:**
   - Feche o XAMPP Control Panel
   - Reinicie o computador
   - Abra XAMPP novamente

2. **Verificar firewall:**
   - Desative temporariamente o firewall
   - Teste a conexão

3. **Verificar antivírus:**
   - Adicione exceções para XAMPP
   - Teste a conexão

4. **Reinstalar XAMPP:**
   - Faça backup dos arquivos
   - Desinstale XAMPP
   - Instale uma nova versão

---

## 📞 SUPORTE

### **URLs Importantes:**
- **Teste de Conexão:** http://localhost/-tica/otica/teste_conexao.php
- **Criar Admin:** http://localhost/-tica/otica/criar_admin.php
- **Login:** http://localhost/-tica/otica/login.php
- **Sistema:** http://localhost/-tica/otica/

### **Arquivos Importantes:**
- `config/database.php` - Configurações do banco
- `otica_db_v2.sql` - Script do banco de dados
- `teste_conexao.php` - Teste de conexão
- `criar_admin.php` - Criar administrador

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
