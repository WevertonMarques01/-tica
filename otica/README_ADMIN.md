# Sistema Administrativo - Ótica

## Configuração Inicial

### 1. Configurar Banco de Dados
1. Importe o arquivo `otica_db.sql` no seu banco de dados MySQL/MariaDB
2. Configure as credenciais do banco no arquivo `config/database.php`

### 2. Credenciais de Acesso
O sistema já está configurado com um usuário administrador padrão:

- **Email:** admin@otica.com
- **Senha:** admin123

> **Nota:** Para conectar com banco de dados real, execute o arquivo `create_admin_user.php` para criar o usuário no banco.

### 3. Acessar o Sistema
1. Acesse: `http://localhost/-tica/otica/login.php`
2. Faça login com as credenciais do administrador
3. Após o login, você será redirecionado para o painel administrativo

## Funcionalidades do Painel Administrativo

### Dashboard Principal
- **Estatísticas em tempo real:** Vendas do dia, novos clientes, produtos em estoque, receita mensal
- **Dados reais do banco:** Todas as estatísticas são buscadas do banco de dados
- **Atividade recente:** Log de ações realizadas no sistema (login, logout, receitas, etc.)
- **Ações rápidas:** Links diretos para as principais funcionalidades
- **Auto-refresh:** Atualização automática a cada 30 segundos

### Menu de Navegação
- **Dashboard:** Página principal com estatísticas em tempo real
- **Nova Venda:** Registrar uma nova venda
- **Histórico:** Visualizar histórico de vendas
- **Clientes:** Gerenciar cadastro de clientes
- **Novo Cliente:** Cadastrar novo cliente
- **Receitas:** Visualizar todas as receitas
- **Nova Receita:** Cadastrar nova receita (prescrição)
- **Financeiro:** Relatórios financeiros
- **Configurações:** Configurações do sistema

### Recursos do Sistema
- **Design responsivo:** Funciona em desktop, tablet e mobile
- **Interface moderna:** Design limpo e profissional
- **Navegação intuitiva:** Menu lateral com todas as funcionalidades
- **Notificações:** Sistema de alertas para ações importantes
- **Logout seguro:** Botão de sair com confirmação

## Estrutura de Arquivos

```
otica/
├── config/
│   ├── database.php          # Configuração do banco de dados
│   └── config.php            # Configurações gerais
├── controllers/
│   ├── LoginController.php   # Controle de autenticação (backup)
│   ├── ClienteController.php # Controle de clientes
│   ├── VendaController.php   # Controle de vendas
│   └── ...
├── views/
│   ├── admin/
│   │   └── index.php         # Painel administrativo principal
│   ├── clientes/
│   │   ├── index.php         # Lista de clientes
│   │   └── novo.php          # Novo cliente
│   ├── vendas/
│   │   ├── nova.php          # Nova venda
│   │   └── historico.php     # Histórico de vendas
│   ├── receitas/
│   │   ├── index.php         # Lista de receitas
│   │   ├── nova.php          # Nova receita
│   │   └── excluir.php       # Excluir receita
│   └── financeiro/
│       └── relatorio.php     # Relatórios financeiros
├── includes/
│   └── auth_check.php        # Verificação de autenticação
├── login.php                 # Página de login (processamento incluído)
├── index.php                 # Página inicial do site
└── create_admin_user.php     # Script para criar usuário admin
```

## Como o Login Funciona

### Processamento do Login
O login é processado diretamente na página `login.php`:
1. **Formulário:** Envia dados via POST para a mesma página
2. **Validação:** Verifica se os campos estão preenchidos
3. **Autenticação:** Compara com credenciais padrão (admin@otica.com / admin123)
4. **Sessão:** Cria variáveis de sessão com dados do usuário
5. **Redirecionamento:** Envia para `views/admin/index.php`

### Credenciais Padrão
- **Email:** admin@otica.com
- **Senha:** admin123

> **Para produção:** Substitua a validação hardcoded por consulta ao banco de dados

## Sistema de Receitas

### Funcionalidades
- **Cadastro de receitas:** Registro completo de prescrições oftalmológicas
- **Dados dos olhos:** Campos específicos para olho direito (OD) e esquerdo (OE)
- **Observações:** Campo para anotações adicionais
- **Associação com clientes:** Cada receita é vinculada a um cliente
- **Data da receita:** Registro da data da prescrição
- **Exclusão segura:** Confirmação antes de excluir receitas

### Estrutura da Receita
- **Cliente:** Seleção obrigatória do cliente
- **Olho Direito (OD):** Esférico, cilíndrico, eixo
- **Olho Esquerdo (OE):** Esférico, cilíndrico, eixo
- **Observações:** Texto livre para anotações
- **Data:** Data da prescrição (padrão: hoje)

### Navegação
- **Listagem:** `views/receitas/index.php`
- **Nova receita:** `views/receitas/nova.php`
- **Exclusão:** `views/receitas/excluir.php`

## Segurança

- **Autenticação obrigatória:** Todas as páginas administrativas requerem login
- **Verificação de sessão:** Sistema verifica se o usuário está logado
- **Logout seguro:** Destrói a sessão ao sair
- **Proteção contra acesso direto:** Arquivos protegidos contra acesso não autorizado

## Personalização

### Cores do Sistema
O sistema usa uma paleta de cores personalizada:
- **Primária:** #28d2c3 (Verde-azulado)
- **Secundária:** #20b8a9 (Verde mais escuro)
- **Acento:** #f4a261 (Laranja)
- **Quente:** #e76f51 (Vermelho-laranja)
- **Sábio:** #a4c3a2 (Verde claro)
- **Creme:** #f7f3e9 (Bege claro)

### Modificando o Design
Para personalizar o design:
1. Edite o arquivo `views/admin/index.php`
2. Modifique as classes CSS e Tailwind
3. Ajuste as cores no arquivo de configuração do Tailwind

## Suporte

Para suporte técnico ou dúvidas:
- Verifique se o banco de dados está configurado corretamente
- Confirme se o usuário administrador foi criado
- Verifique as permissões dos arquivos no servidor
- Consulte os logs de erro do PHP para problemas específicos

## Próximos Passos

O sistema está configurado apenas com o frontend. Para completar a implementação:

1. **Backend:** Implementar as funcionalidades de CRUD nos controllers
2. **Banco de dados:** Conectar as páginas com o banco de dados
3. **Validações:** Adicionar validações de formulários
4. **Relatórios:** Implementar geração de relatórios
5. **Upload de arquivos:** Sistema para upload de imagens de produtos
6. **Notificações:** Sistema de notificações em tempo real 