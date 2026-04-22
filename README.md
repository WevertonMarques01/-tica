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

1. Crie um banco de dados MySQL (ex: `otica_db`)
2. Importe o arquivo `otica_db.sql` via phpMyAdmin ou similar
3. As credenciais padrão do banco no `config/database.php` são:
   - Host: `localhost`
   - Banco: `otica_db`
   - Usuário: `root`
   - Senha: (vazio)
   
   **Para InfinityFree:** Edite `config/database.php` com as credenciais fornecidas.

### 2. Ajuste de Configuração

Edite `config/database.php` se necessário, ou crie `config/database_local.php` (não versionado) para suas credenciais.

## 📁 Estrutura de Arquivos

```
├── .htaccess                # Configurações de segurança e performance
├── index.php                 # Site público (landing page)
├── login.php                 # Página de login
├── config/
│   ├── database.php          # Configuração do banco de dados
│   ├── database_local.php.example
│   ├── config.php            # Configurações gerais (SITE_URL dinâmico)
│   ├── database_compatibility.php
│   └── db_compat.php
├── controllers/
│   ├── ClienteController.php # Controller AJAX para clientes
│   ├── ComprovanteController.php
│   ├── LoginController.php   # Controller AJAX para login/logout
│   └── UsuarioController.php # Controller AJAX para usuários
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
│   │   └── clean-ui.css
│   └── js/
│       ├── auto-fill-client.js
│       ├── notifications.js
│       └── receita-utils.js
├── img/
│   ├── 1.png
│   └── carrosel/
├── uploads/
│   ├── .htaccess            # Bloqueia execução de PHP
│   └── comprovantes/
├── includes/
│   ├── auth_check.php
│   └── notificacao.php
├── otica_db.sql             # Backup do banco (protegido por .htaccess)
├── DEPLOY.md                # Guia de deploy
├── INSTALL.txt              # Instalação rápida
└── README.md                # Este arquivo
```

**Notas:**
- `views/vendas/` e `views/financeiro/` contêm `index.php` que redirecionam para `/views/admin/` (evitam 403).
- A raiz contém `index.php` (landing page) e `login.php`.

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