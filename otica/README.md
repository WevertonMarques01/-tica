# Sistema Ótica - Site e Painel Administrativo

## 📋 Descrição

Sistema completo para ótica com site público e painel administrativo. O site possui design moderno e responsivo, inspirado no layout do salaberga.com, com foco em UI/UX clean.

## ✨ Características

### 🎨 Site Público (`index.php`)
- Design moderno e responsivo
- Layout inspirado no salaberga.com
- Cores principais: #28d2c3 (verde água)
- Botão "Entrar" no header para acesso administrativo
- Seções: Hero, Features, Serviços
- Animações suaves e efeitos visuais

### 🔐 Sistema de Login (`login.php`)
- Página de login dedicada
- Design elegante com animações
- Validação de formulário
- Indicador de força da senha
- Mensagens de erro/sucesso
- Responsivo para todos os dispositivos

### 🛠️ Painel Administrativo
- Controle de usuários
- Gestão de clientes
- Sistema de vendas
- Relatórios financeiros
- Ordem de serviços

## 🚀 Instalação

### 1. Configuração do Banco de Dados

1. Crie um banco de dados MySQL chamado `otica_db`
2. Execute o script SQL:
   ```sql
   -- Importe o arquivo otica_db.sql
   -- Execute o script setup_users.sql para criar usuários
   ```

### 2. Configuração do PHP

1. Certifique-se de que o PHP está configurado
2. Configure as credenciais do banco em `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'otica_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### 3. Usuários Padrão

Após executar o script SQL, você terá acesso com:

**Administrador:**
- Email: `admin@otica.com`
- Senha: `admin123`

**Usuário Teste:**
- Email: `teste@otica.com`
- Senha: `teste123`

## 📁 Estrutura de Arquivos

```
otica/
├── index.php              # Site público
├── login.php              # Página de login
├── config/
│   ├── database.php       # Configuração do banco
│   └── config.php         # Configurações gerais
├── controllers/
│   ├── LoginController.php # Controlador de login
│   ├── ClienteController.php
│   ├── VendaController.php
│   └── ...
├── models/
│   ├── Usuario.php
│   ├── Cliente.php
│   └── ...
├── views/
│   ├── clientes/
│   ├── vendas/
│   └── ...
├── setup_users.sql        # Script para criar usuários
└── otica_db.sql          # Estrutura do banco
```

## 🎯 Como Usar

### Acesso ao Site
1. Acesse `index.php` no navegador
2. Navegue pelas seções do site
3. Clique em "Entrar" no header para acessar o painel

### Acesso Administrativo
1. Clique em "Entrar" no header do site
2. Use as credenciais fornecidas
3. Acesse o painel administrativo

## 🎨 Design System

### Cores Principais
- **Primária:** #28d2c3 (Verde água)
- **Secundária:** #20b8a9 (Verde escuro)
- **Acento:** #f4a261 (Laranja)
- **Fundo:** #ffffff (Branco)

### Tipografia
- **Títulos:** Comfortaa (Google Fonts)
- **Texto:** Nunito (Google Fonts)

### Componentes
- Botões com gradientes e hover effects
- Inputs com animações de foco
- Cards com sombras suaves
- Animações de entrada e saída

## 🔧 Tecnologias Utilizadas

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 7.4+
- **Banco de Dados:** MySQL 5.7+
- **Frameworks:** Tailwind CSS, Font Awesome
- **Fontes:** Google Fonts (Comfortaa, Nunito)

## 📱 Responsividade

O site é totalmente responsivo e funciona em:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (320px - 767px)

## 🔒 Segurança

- Senhas criptografadas com bcrypt
- Validação de entrada
- Proteção contra SQL Injection
- Sessões seguras
- Sanitização de dados

## 🚀 Próximos Passos

1. Implementar painel administrativo completo
2. Adicionar sistema de relatórios
3. Implementar backup automático
4. Adicionar sistema de notificações
5. Implementar API REST

## 📞 Suporte

Para dúvidas ou suporte, entre em contato:
- Email: suporte@otica.com
- Documentação: [Link para documentação]

---

**Desenvolvido com ❤️ para o sistema da ótica** 