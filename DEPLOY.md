# Guia de Deploy no InfinityFree

## 📋 Pré-requisitos

### Requisitos do Servidor
- **PHP**: 7.4 ou superior (recomendado 8.0+)
- **Extensões PHP obrigatórias**:
  - `PDO` (PHP Data Objects)
  - `PDO_MySQL` (driver MySQL para PDO)
  - `mbstring` (para manipulação de strings multibyte)
  - `fileinfo` (para verificação de MIME type nos uploads)
  - `session` (gerenciamento de sessões)
  - `json` (para APIs e tratamento de dados)
- **MySQL**: 5.7 ou superior (MariaDB 10.4+ compatível)
- **Apache** com mod_rewrite (opcional, para URLs amigáveis)

### No InfinityFree
- PHP 8.2 disponível por padrão
- MySQL/MariaDB incluído
- Suporte a .htaccess (Apache)
- Limite de upload: 10MB (configurado no projeto)

---

Este guia explica como hospedar o Sistema Ótica no InfinityFree (hospedagem gratuita).

## 📋 Pré-requisitos

- Conta no InfinityFree (https://infinityfree.net)
- Cliente FTP (como FileZilla) ou gerenciador de arquivos do painel
- Acesso ao phpMyAdmin (ou similar) para importar o banco de dados

## 🚀 Passos para Deploy

### 1. Prepare o Servidor

1. Acesse o painel do InfinityFree
2. Crie um novo banco de dados MySQL:
   - Vá em **MySQL Databases**
   - Crie um banco de dados (ex: `otica_db`)
   - Anote as credenciais:
     - Nome do banco
     - Usuário do banco
     - Senha do banco
     - Host (geralmente `localhost`)

### 2. Configure o Projeto

1. Edite o arquivo `config/database.php`
2. Atualize as constantes com as credenciais do InfinityFree:
   ```php
   define('DB_HOST', 'localhost'); // ou o host fornecido
   define('DB_NAME', 'nome_do_seu_banco');
   define('DB_USER', 'seu_usuario_mysql');
   define('DB_PASS', 'sua_senha_mysql');
   ```
3. Opcional: Crie `config/database_local.php` (não versionado) com:
   ```php
   <?php
   define('DB_NAME', 'seu_banco');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   ```

### 3. Importe o Banco de Dados

1. Acesse o phpMyAdmin do InfinityFree
2. Selecione o banco de dados criado
3. Importe o arquivo `otica_db.sql`
4. Aguarde a conclusão

**Nota:** Se encontrar erros de timeout, importe em partes ou use um cliente MySQL desktop.

### 4. Faça o Upload dos Arquivos

Estrutura atual do projeto (após reorganização):
```
projeto/
├── .htaccess
├── index.php              ← SITE PÚBLICO (landing page)
├── login.php              ← LOGIN
├── config/
├── controllers/
├── models/
├── views/
├── assets/
├── img/
├── uploads/
├── includes/
├── otica_db.sql
├── DEPLOY.md
└── README.md
```

**Faça upload de TODOS esses arquivos e pastas para `/public_html/`** (raiz do site no InfinityFree).

Atenção: Certifique-se de que, após o upload, o arquivo `index.php` esteve em `public_html/index.php` (não dentro de uma subpasta).

### 5. Ajuste Permissões

A pasta `uploads/` precisa ter permissão de escrita. No InfinityFree geralmente já funciona, mas seocurrer erro:
- Defina permissão 755 ou 777 para a pasta `uploads/` e subpastas (via gerenciador de arquivos ou FTP).

### 6. Acesse o Site

1. Acesse seu domínio: `https://seunome.epizy.com`
2. A **página pública** (landing page) deve aparecer com o botão "Entrar"
3. Clique em "Entrar" para acessar o painel administrativo
4. Use as credenciais:
   - Email: `admin@otica.com`
   - Senha: `admin123`

**URLs do sistema:**
- Página pública (home): `/` ou `/index.php`
- Login: `/login.php`
- Painel Admin: `/views/admin/index.php`
- Clientes: `/views/clientes/index.php`
- Produtos: `/views/produtos/index.php`
- Vendas: `/views/vendas/nova.php` (criar) ou `/views/vendas/historico.php`
- Agendamentos: `/views/agendamentos/index.php`

(O sistema usa acesso direto aos arquivos PHP; não há roteamento por .htaccess.)

## 📦 Estrutura de Arquivos Após Deploy

```
public_html/
├── .htaccess                 # Segurança e performance
├── index.php                 # Landing page (site público)
├── login.php                 # Página de login
├── config/
│   ├── database.php          # Credenciais do banco (editar!)
│   ├── database_local.php.example
│   ├── config.php            # Configurações (SITE_URL dinâmico)
│   ├── database_compatibility.php
│   └── db_compat.php
├── controllers/
│   ├── ClienteController.php # AJAX
│   ├── ComprovanteController.php
│   ├── LoginController.php   # Auth
│   └── UsuarioController.php # AJAX
├── models/
│   ├── BaseModel.php
│   └── AgendamentoModel.php
├── views/
│   ├── admin/
│   │   ├── index.php
│   │   └── funcionarios.php
│   ├── agendamentos/
│   │   ├── index.php
│   │   ├── novo.php
│   │   ├── concluir.php
│   │   ├── cancelar.php
│   │   ├── excluir.php
│   │   ├── compartilhar_whatsapp.php
│   │   └── get_agendamento.php
│   ├── clientes/
│   │   ├── index.php
│   │   ├── novo.php
│   │   ├── visualizar.php
│   │   ├── editar.php
│   │   ├── excluir.php
│   │   └── imprimir.php
│   ├── comprovantes/
│   │   ├── index.php
│   │   ├── novo.php
│   │   ├── visualizar.php
│   │   ├── excluir.php
│   │   └── salvar.php
│   ├── financeiro/
│   │   ├── relatorio.php
│   │   └── imprimir.php
│   ├── produtos/
│   │   ├── index.php
│   │   ├── novo.php         # Criar/Editar
│   │   ├── visualizar.php
│   │   ├── excluir.php
│   │   └── verificar_codigo.php
│   ├── receitas/
│   │   ├── index.php
│   │   ├── nova.php
│   │   ├── excluir.php
│   │   └── compartilhar_whatsapp.php
│   ├── vendas/
│   │   ├── index.php         # Redireciona para admin
│   │   ├── nova.php
│   │   ├── visualizar.php
│   │   ├── editar.php
│   │   ├── excluir.php
│   │   └── historico.php
│   ├── layout_base.php
│   └── layout_end.php
├── assets/
│   ├── css/
│   └── js/
├── img/
│   ├── 1.png
│   └── carrosel/
├── uploads/
│   ├── .htaccess            # Bloqueia execução de PHP
│   └── comprovantes/
├── includes/
│   ├── auth_check.php
│   └── notificacao.php
├── otica_db.sql             # Backup (protegido por .htaccess)
└── README.md
```

**Notas importantes:**
- A raiz do site (`public_html/`) contém `index.php` (landing page) e `login.php`.
- O painel administrativo fica em `views/admin/index.php`.
- Os diretórios `views/vendas/` e `views/financeiro/` contêm `index.php` que redirecionam para `/views/admin/` (evitam 403).


Para atualizar o sistema:
1. Faça backup do banco de dados
2. Substitua os arquivos via FTP
3. Execute scripts de migração se houver

---

**Desenvolvido para Wiz Óptica**
