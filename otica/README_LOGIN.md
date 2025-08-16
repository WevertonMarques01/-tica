# Sistema de Login - Ótica

## Como Funciona

O sistema de login da ótica utiliza autenticação segura baseada em banco de dados.

### Estrutura

- **Arquivo de login**: `login.php`
- **Verificação de autenticação**: `includes/auth_check.php`
- **Banco de dados**: Tabela `usuarios`

### Credenciais de Acesso

- **Email**: admin@otica.com
- **Senha**: admin123

### Como Acessar

1. Acesse: `http://localhost/-tica/otica/login.php`
2. Digite as credenciais acima
3. Será redirecionado para o painel administrativo

### Segurança

- Senhas são hasheadas usando `password_hash()`
- Verificação segura com `password_verify()`
- Sessões PHP para controle de acesso
- Verificação de permissões por nível (1 = admin)

### Estrutura do Banco

```sql
CREATE TABLE usuarios (
  id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  nome varchar(100) NOT NULL,
  email varchar(100) NOT NULL UNIQUE,
  senha_hash text NOT NULL,
  permissao int(11) NOT NULL,
  criado_em timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
);
```

### Arquivos Principais

- `login.php` - Página de login
- `includes/auth_check.php` - Verificação de autenticação
- `views/admin/index.php` - Painel administrativo
- `controllers/LoginController.php` - Controller de login 