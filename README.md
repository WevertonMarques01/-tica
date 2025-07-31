# Aplicação PHP MVC

Uma aplicação PHP completa seguindo o padrão Model-View-Controller (MVC) com funcionalidades de CRUD para usuários.

## 📁 Estrutura do Projeto

```
-tica/
├── config/                 # Configurações da aplicação
│   └── config.php         # Configurações principais
├── controller/            # Controllers da aplicação
│   ├── HomeController.php # Controller da página inicial
│   └── UserController.php # Controller de usuários
├── core/                  # Classes principais do framework
│   ├── Controller.php     # Classe base para controllers
│   └── Router.php         # Sistema de roteamento
├── database/              # Arquivos do banco de dados
│   └── schema.sql        # Esquema do banco
├── model/                 # Models da aplicação
│   ├── BaseModel.php     # Classe base para models
│   └── UserModel.php     # Model de usuários
├── view/                  # Views da aplicação
│   ├── layout/           # Layouts compartilhados
│   │   ├── header.php    # Cabeçalho
│   │   └── footer.php    # Rodapé
│   ├── home/             # Views da página inicial
│   │   └── index.php     # Página inicial
│   ├── user/             # Views de usuários
│   │   ├── index.php     # Lista de usuários
│   │   └── create.php    # Criar usuário
│   └── error/            # Páginas de erro
│       └── 404.php       # Página 404
├── .htaccess             # Configurações do Apache
├── index.php             # Ponto de entrada da aplicação
└── README.md             # Este arquivo
```

## 🚀 Instalação

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache com mod_rewrite habilitado

### Passos para instalação

1. **Clone ou baixe o projeto**
   ```bash
   git clone [url-do-repositorio]
   cd -tica
   ```

2. **Configure o banco de dados**
   - Crie um banco de dados MySQL
   - Execute o arquivo `database/schema.sql` para criar as tabelas

3. **Configure as credenciais do banco**
   - Edite o arquivo `config/config.php`
   - Atualize as constantes `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS`

4. **Configure o servidor web**
   - Certifique-se de que o mod_rewrite está habilitado no Apache
   - O DocumentRoot deve apontar para a pasta do projeto

## 🛠️ Funcionalidades

### ✅ Implementadas

- **Sistema MVC completo** com separação clara de responsabilidades
- **Sistema de rotas** automático baseado em URLs amigáveis
- **CRUD de usuários** com validação de dados
- **Layout responsivo** usando Bootstrap 5
- **Conexão segura com banco** usando PDO
- **Hash de senhas** usando password_hash()
- **Validação de formulários** no frontend e backend
- **Páginas de erro** personalizadas

### 🔄 Rotas Disponíveis

- `/` - Página inicial
- `/home` - Página inicial (alternativa)
- `/about` - Página sobre
- `/contact` - Página de contato
- `/users` - Lista de usuários
- `/users/create` - Criar usuário
- `/users/edit?id=X` - Editar usuário
- `/users/delete?id=X` - Excluir usuário

## 📝 Como Usar

### Criando um novo Controller

```php
<?php
class MeuController extends Controller
{
    public function indexAction()
    {
        $data = ['title' => 'Minha Página'];
        $this->render('minha/view', $data);
    }
}
?>
```

### Criando um novo Model

```php
<?php
class MeuModel extends BaseModel
{
    protected $table = 'minha_tabela';
    
    public function __construct()
    {
        parent::__construct();
    }
}
?>
```

### Criando uma nova View

```php
<?php include 'view/layout/header.php'; ?>

<div class="container">
    <h1><?= $title ?></h1>
    <!-- Seu conteúdo aqui -->
</div>

<?php include 'view/layout/footer.php'; ?>
```

## 🔧 Configurações

### Banco de Dados

Edite o arquivo `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sua_database');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### Aplicação

```php
define('APP_NAME', 'Minha Aplicação MVC');
define('APP_URL', 'http://localhost');
define('DEBUG_MODE', true);
```

## 🎨 Personalização

### Cores e Estilo

Edite o arquivo `view/layout/header.php` para personalizar:
- Cores do Bootstrap
- Logo da aplicação
- Menu de navegação

### Layout

- **Header**: `view/layout/header.php`
- **Footer**: `view/layout/footer.php`
- **CSS personalizado**: Adicione no header.php

## 🔒 Segurança

- **Senhas**: Hash usando `password_hash()`
- **SQL Injection**: Protegido com PDO prepared statements
- **XSS**: Escape de dados com `htmlspecialchars()`
- **CSRF**: Token implementado (pode ser expandido)

## 📚 Estrutura MVC

### Model (Modelo)
- Gerencia dados e lógica de negócio
- Interage com o banco de dados
- Valida dados

### View (Visualização)
- Apresenta dados ao usuário
- Interface do usuário
- Templates HTML

### Controller (Controlador)
- Processa requisições
- Coordena Model e View
- Lógica de aplicação

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Se você encontrar algum problema ou tiver dúvidas, abra uma issue no repositório.

---

**Desenvolvido com ❤️ em PHP MVC** 