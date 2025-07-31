# Sistema de Gestão Óptica

Uma aplicação PHP completa para gestão de uma óptica, incluindo controle de clientes, vendas, produtos, ordens de serviço e financeiro.

## 📁 Estrutura do Projeto

```
-tica/
├── otica/                 # Aplicação principal
│   ├── controllers/       # Controllers da aplicação
│   │   ├── ClienteController.php
│   │   ├── FinanceiroController.php
│   │   ├── LoginController.php
│   │   ├── OrdemServicoController.php
│   │   ├── ProdutoController.php
│   │   └── VendaController.php
│   ├── models/           # Models da aplicação
│   │   ├── Cliente.php
│   │   ├── OrdemServico.php
│   │   ├── Produto.php
│   │   ├── Usuario.php
│   │   └── Venda.php
│   └── views/            # Views da aplicação
│       ├── clientes/     # Views de clientes
│       │   ├── index.php
│       │   └── novo.php
│       ├── financeiro/   # Views financeiras
│       │   └── relatorio.php
│       ├── layout/       # Layouts compartilhados
│       │   ├── header.php
│       │   └── footer.php
│       └── vendas/       # Views de vendas
│           ├── historico.php
│           └── nova.php
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
   - Execute o arquivo de schema para criar as tabelas

3. **Configure as credenciais do banco**
   - Edite o arquivo de configuração
   - Atualize as constantes de conexão com o banco

4. **Configure o servidor web**
   - Certifique-se de que o mod_rewrite está habilitado no Apache
   - O DocumentRoot deve apontar para a pasta do projeto

## 🛠️ Funcionalidades

### ✅ Módulos Implementados

- **Gestão de Clientes**
  - Cadastro de clientes
  - Listagem e busca
  - Edição de dados

- **Gestão de Produtos**
  - Cadastro de produtos/óculos
  - Controle de estoque
  - Categorização

- **Vendas**
  - Registro de vendas
  - Histórico de vendas
  - Relatórios

- **Ordens de Serviço**
  - Criação de OS
  - Acompanhamento de status
  - Gestão de prazos

- **Financeiro**
  - Relatórios financeiros
  - Controle de receitas
  - Análise de vendas

- **Sistema de Login**
  - Autenticação de usuários
  - Controle de acesso

### 🔄 Rotas Disponíveis

- `/clientes` - Gestão de clientes
- `/clientes/novo` - Cadastrar novo cliente
- `/produtos` - Gestão de produtos
- `/vendas` - Gestão de vendas
- `/vendas/nova` - Nova venda
- `/vendas/historico` - Histórico de vendas
- `/ordens-servico` - Gestão de OS
- `/financeiro/relatorio` - Relatórios financeiros
- `/login` - Sistema de login

## 📝 Como Usar

### Acessando o Sistema

1. Acesse a URL do projeto no navegador
2. Faça login com suas credenciais
3. Navegue pelos módulos disponíveis

### Gestão de Clientes

- Acesse o módulo "Clientes"
- Use "Novo Cliente" para cadastrar
- Utilize a busca para encontrar clientes existentes

### Registro de Vendas

- Acesse "Vendas" → "Nova Venda"
- Selecione o cliente
- Adicione os produtos
- Finalize a venda

### Relatórios Financeiros

- Acesse "Financeiro" → "Relatórios"
- Visualize dados de vendas e receitas
- Exporte relatórios conforme necessário

## 🔧 Configurações

### Banco de Dados

Configure as credenciais do banco de dados no arquivo de configuração:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sua_database');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### Aplicação

```php
define('APP_NAME', 'Sistema de Gestão Óptica');
define('APP_URL', 'http://localhost');
define('DEBUG_MODE', true);
```

## 🎨 Personalização

### Layout e Cores

- Edite os arquivos em `otica/views/layout/`
- Personalize cores e estilos no CSS
- Modifique o logo e branding

### Funcionalidades

- Adicione novos módulos conforme necessário
- Personalize relatórios
- Configure permissões de usuário

## 🔒 Segurança

- **Autenticação**: Sistema de login seguro
- **Senhas**: Hash usando `password_hash()`
- **SQL Injection**: Protegido com prepared statements
- **XSS**: Escape de dados com `htmlspecialchars()`
- **Headers de Segurança**: Configurados no .htaccess

## 📚 Estrutura MVC

### Model (Modelo)
- `Cliente.php` - Gestão de dados de clientes
- `Produto.php` - Gestão de produtos/óculos
- `Venda.php` - Gestão de vendas
- `OrdemServico.php` - Gestão de OS
- `Usuario.php` - Gestão de usuários

### View (Visualização)
- `views/clientes/` - Interface de clientes
- `views/vendas/` - Interface de vendas
- `views/financeiro/` - Interface financeira
- `views/layout/` - Layouts compartilhados

### Controller (Controlador)
- `ClienteController.php` - Lógica de clientes
- `VendaController.php` - Lógica de vendas
- `FinanceiroController.php` - Lógica financeira
- `LoginController.php` - Autenticação

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/NovaFuncionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/NovaFuncionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Se você encontrar algum problema ou tiver dúvidas, abra uma issue no repositório.

---

**Sistema de Gestão Óptica - Desenvolvido com ❤️ em PHP MVC** 