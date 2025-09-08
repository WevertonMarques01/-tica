# Sistema de Cadastro de Produtos - Wiz Ótica

## 📋 Visão Geral

Sistema completo de cadastro de produtos com suporte a leitor de código de barras, desenvolvido para a Wiz Ótica.

## 🎯 Funcionalidades Principais

### ✅ Cadastro de Produtos
- **Código de Barras**: Campo otimizado para leitores de código de barras
- **Nome do Produto**: Campo obrigatório
- **Descrição**: Campo opcional para detalhes
- **Categoria**: Seleção de categoria (opcional)
- **Estoque**: Quantidade em estoque
- **Preço**: Preço do produto em reais

### 🔍 Validações Implementadas
- Código de barras obrigatório
- Apenas números no código de barras
- Verificação de duplicidade de código
- Nome do produto obrigatório
- Estoque não pode ser negativo
- Preço deve ser maior que zero

### 📱 Compatibilidade com Leitores
- **Detecção automática** de leitores de código de barras
- **Suporte universal** para diferentes modelos
- **Fallback manual** para digitação
- **Auto-focus** inteligente entre campos

## 🚀 Como Usar

### 1. Acesso ao Sistema
```
URL: http://localhost/-tica/-tica/otica/views/produtos/novo.php
Login: admin@otica.com / admin123
```

### 2. Fluxo de Cadastro

#### Com Leitor de Código de Barras:
1. Abra a página de cadastro
2. O campo "Código de Barras" estará focado automaticamente
3. Passe o produto no leitor → código é preenchido automaticamente
4. O foco muda automaticamente para o campo "Nome"
5. Preencha os demais campos
6. Clique em "Salvar Produto"

#### Sem Leitor (Digitação Manual):
1. Digite o código de barras no campo
2. Apenas números são aceitos
3. Preencha os demais campos
4. Clique em "Salvar Produto"

### 3. Categorias Disponíveis
- Óculos de Grau
- Óculos de Sol
- Lentes de Contato
- Armações
- Lentes
- Acessórios
- Produtos de Limpeza
- Estojos
- Cordões
- Outros

## 🔧 Características Técnicas

### Frontend
- **Framework**: Tailwind CSS
- **Ícones**: Font Awesome 6
- **Responsivo**: Mobile-first design
- **JavaScript**: Vanilla JS (sem dependências)

### Backend
- **Linguagem**: PHP 8.0+
- **Banco**: MySQL/MariaDB
- **Padrão**: MVC simplificado
- **Segurança**: Validação server-side

### Banco de Dados
```sql
-- Tabela produtos
CREATE TABLE produtos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria_id BIGINT UNSIGNED,
    estoque INT NOT NULL DEFAULT 0,
    preço DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela categorias_produtos
CREATE TABLE categorias_produtos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 🎨 Interface do Usuário

### Página de Listagem (`index.php`)
- **Dashboard** com estatísticas
- **Tabela** de produtos com ações
- **Filtros** por categoria e estoque
- **Ações**: Editar e Excluir

### Página de Cadastro (`novo.php`)
- **Formulário** intuitivo
- **Validação** em tempo real
- **Feedback** visual de sucesso/erro
- **Auto-focus** inteligente

## 🔒 Segurança

### Validações Implementadas
- **SQL Injection**: Prepared statements
- **XSS**: Escape de HTML
- **CSRF**: Tokens de sessão
- **Autenticação**: Verificação de login

### Permissões
- Apenas usuários autenticados
- Verificação de perfil admin
- Logs de atividades

## 📊 Relatórios e Estatísticas

### Dashboard
- Total de produtos
- Produtos em estoque
- Produtos sem estoque
- Categorias ativas

### Listagem
- Código de barras
- Nome do produto
- Categoria
- Estoque atual
- Preço
- Ações disponíveis

## 🛠️ Manutenção

### Logs
- Atividades de cadastro
- Alterações de estoque
- Acessos ao sistema

### Backup
- Estrutura do banco
- Dados de produtos
- Configurações

## 🚨 Troubleshooting

### Problemas Comuns

#### Leitor não funciona:
1. Verifique se o leitor está conectado
2. Teste em um editor de texto
3. Verifique configurações do leitor
4. Use digitação manual como alternativa

#### Código duplicado:
1. Verifique se o produto já existe
2. Use busca por código
3. Considere editar produto existente

#### Erro de conexão:
1. Verifique se o MySQL está rodando
2. Confirme credenciais do banco
3. Teste conexão com `test_connection.php`

## 📞 Suporte

Para suporte técnico:
- **Email**: suporte@otica.com
- **Documentação**: Este arquivo
- **Logs**: Verificar arquivos de log do sistema

## 🔄 Atualizações Futuras

### Funcionalidades Planejadas
- [ ] Importação em lote via CSV
- [ ] Geração de códigos de barras
- [ ] Relatórios avançados
- [ ] Integração com vendas
- [ ] Controle de estoque mínimo
- [ ] Alertas de estoque baixo

### Melhorias Técnicas
- [ ] API REST
- [ ] Cache de consultas
- [ ] Otimização de performance
- [ ] Backup automático
- [ ] Logs estruturados

---

**Desenvolvido para Wiz Ótica**  
*Sistema de Gestão Completo*
