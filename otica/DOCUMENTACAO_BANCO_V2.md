# DOCUMENTAÇÃO - BANCO DE DADOS V2.0
## Sistema de Ótica - Banco Relacional Otimizado

### 📋 RESUMO EXECUTIVO

O banco de dados V2.0 foi criado com base na análise completa do sistema existente, incorporando todos os campos, formulários e relacionamentos identificados. Esta nova versão oferece uma estrutura relacional completa, otimizada e profissional, pronta para uso em produção.

---

## 🚀 PRINCIPAIS MELHORIAS

### 1. **Estrutura Relacional Completa**
- ✅ 15 tabelas interligadas com relacionamentos bem definidos
- ✅ Chaves estrangeiras para integridade referencial
- ✅ Normalização adequada para evitar redundância
- ✅ Suporte a transações ACID

### 2. **Campos e Funcionalidades Expandidas**
- ✅ Todos os campos dos formulários existentes incorporados
- ✅ Campos adicionais para funcionalidades avançadas
- ✅ Suporte a JSON para dados flexíveis (armações, lentes, etc.)
- ✅ Campos de auditoria (created_at, updated_at)

### 3. **Otimização de Performance**
- ✅ Índices estratégicos para consultas frequentes
- ✅ Índices compostos para otimização
- ✅ Views para relatórios complexos
- ✅ Procedures para operações comuns

### 4. **Automação e Triggers**
- ✅ Geração automática de números de venda e OS
- ✅ Controle automático de estoque
- ✅ Histórico automático de mudanças de status
- ✅ Logs automáticos de atividades

---

## 📊 ESTRUTURA DAS TABELAS

### 🔐 **usuarios**
**Propósito**: Gerenciamento de usuários do sistema
- **Campos principais**: id, nome, email, senha, perfil, ativo
- **Perfis**: admin, vendedor, optico, gerente
- **Segurança**: Senhas hasheadas, controle de acesso

### 👥 **clientes**
**Propósito**: Cadastro completo de clientes
- **Campos principais**: id, nome, documento, email, telefone, endereço completo
- **Funcionalidades**: Suporte a CPF/CNPJ, dados de contato, endereço completo
- **Relacionamentos**: Receitas, vendas, ordens de serviço

### 📦 **categorias_produtos**
**Propósito**: Categorização de produtos
- **Campos**: id, nome, descrição, ativo
- **Exemplos**: Óculos de Grau, Óculos de Sol, Lentes, Armações

### 🏷️ **marcas**
**Propósito**: Marcas de produtos
- **Campos**: id, nome, descrição, ativo
- **Exemplos**: Ray-Ban, Oakley, Hoya, Essilor

### 🛍️ **produtos**
**Propósito**: Cadastro completo de produtos
- **Campos principais**: código, nome, categoria, marca, preços, estoque
- **Funcionalidades**: Controle de estoque, preços múltiplos, código de barras
- **Relacionamentos**: Categorias, marcas, itens de venda

### 👁️ **receitas**
**Propósito**: Receitas oftalmológicas dos clientes
- **Campos principais**: Dados completos dos olhos (OD/OE), tratamentos, armações
- **Funcionalidades**: Dados do paciente, fiador, tratamentos especiais
- **Campos JSON**: Armações selecionadas, lentes selecionadas, tipos de lentes

### 💰 **vendas**
**Propósito**: Registro de vendas
- **Campos principais**: número, cliente, produtos, valores, pagamento
- **Funcionalidades**: Descontos, parcelamento, múltiplas formas de pagamento
- **Relacionamentos**: Cliente, usuário, receita, itens

### 🛒 **itens_venda**
**Propósito**: Itens de cada venda
- **Campos**: produto, quantidade, preços, descontos
- **Funcionalidades**: Controle individual de itens, descontos por item

### 🔧 **ordens_servico**
**Propósito**: Ordens de serviço para fabricação
- **Campos principais**: número, cliente, descrição, status, prioridade
- **Funcionalidades**: Controle de status, prioridades, datas de entrega
- **Relacionamentos**: Cliente, receita, venda, histórico

### 📋 **historico_ordens**
**Propósito**: Histórico de mudanças de status
- **Campos**: ordem, usuário, status anterior/novo, observações
- **Funcionalidades**: Rastreamento completo de mudanças

### 💳 **pagamentos**
**Propósito**: Controle de pagamentos
- **Campos**: venda, parcelas, vencimentos, status
- **Funcionalidades**: Controle de parcelamento, vencimentos

### 📦 **movimentacao_estoque**
**Propósito**: Controle de movimentação de estoque
- **Campos**: produto, tipo, quantidade, motivo
- **Funcionalidades**: Rastreamento completo de movimentações

### 📝 **logs_sistema**
**Propósito**: Logs de atividades do sistema
- **Campos**: usuário, ação, dados, IP, user agent
- **Funcionalidades**: Auditoria completa, segurança

### ⚙️ **configuracoes**
**Propósito**: Configurações do sistema
- **Campos**: chave, valor, tipo, categoria
- **Funcionalidades**: Configurações flexíveis por categoria

---

## 🔗 RELACIONAMENTOS PRINCIPAIS

### Cliente → Receitas → Ordens de Serviço
```
clientes (1) ←→ (N) receitas (1) ←→ (N) ordens_servico
```

### Cliente → Vendas → Itens de Venda
```
clientes (1) ←→ (N) vendas (1) ←→ (N) itens_venda ←→ (1) produtos
```

### Produtos → Categorias e Marcas
```
produtos (N) ←→ (1) categorias_produtos
produtos (N) ←→ (1) marcas
```

### Ordens de Serviço → Histórico
```
ordens_servico (1) ←→ (N) historico_ordens
```

---

## 🎯 FUNCIONALIDADES AVANÇADAS

### 1. **Controle de Estoque Automático**
- ✅ Atualização automática ao vender
- ✅ Movimentações rastreadas
- ✅ Alertas de estoque baixo
- ✅ Histórico completo

### 2. **Sistema de Receitas Completo**
- ✅ Dados completos dos olhos (OD/OE)
- ✅ Tratamentos especiais (antirreflexo, fotossensível, etc.)
- ✅ Armações e lentes em JSON
- ✅ Controle de validade

### 3. **Gestão de Vendas Avançada**
- ✅ Múltiplas formas de pagamento
- ✅ Sistema de parcelamento
- ✅ Descontos por item e venda
- ✅ Controle de status

### 4. **Ordens de Serviço Profissionais**
- ✅ Controle de prioridades
- ✅ Histórico de mudanças
- ✅ Previsão de entrega
- ✅ Observações internas

### 5. **Auditoria e Segurança**
- ✅ Logs completos de atividades
- ✅ Rastreamento de IP e user agent
- ✅ Histórico de mudanças
- ✅ Controle de acesso por perfil

---

## 📈 OTIMIZAÇÕES DE PERFORMANCE

### Índices Estratégicos
- ✅ Índices simples para campos de busca
- ✅ Índices compostos para consultas complexas
- ✅ Índices para relacionamentos frequentes

### Views Otimizadas
- ✅ `vw_dashboard_vendas`: Dashboard de vendas
- ✅ `vw_produtos_estoque_baixo`: Produtos com estoque baixo
- ✅ `vw_ordens_pendentes`: Ordens pendentes

### Procedures Úteis
- ✅ `sp_relatorio_vendas_periodo`: Relatório de vendas
- ✅ `sp_relatorio_estoque_baixo`: Relatório de estoque
- ✅ `sp_relatorio_receitas_vencidas`: Receitas vencidas

---

## 🔧 TRIGGERS AUTOMÁTICOS

### 1. **Geração de Números**
- ✅ Números de venda automáticos (VDA + ano + mês + sequencial)
- ✅ Números de OS automáticos (OS + ano + mês + sequencial)

### 2. **Controle de Estoque**
- ✅ Atualização automática ao vender
- ✅ Registro de movimentação
- ✅ Controle de quantidade anterior/atual

### 3. **Histórico de Ordens**
- ✅ Registro automático de mudanças de status
- ✅ Rastreamento de usuário responsável

---

## 📊 DADOS INICIAIS

### Usuário Administrador
- **Email**: admin@otica.com
- **Senha**: admin123 (hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
- **Perfil**: admin

### Categorias de Produtos
- Óculos de Grau
- Óculos de Sol
- Lentes
- Armações
- Acessórios
- Produtos de Limpeza

### Marcas Populares
- Ray-Ban, Oakley, Hoya, Essilor, Zeiss, Carrera, Polaroid, Vogue

### Configurações do Sistema
- Dados da empresa
- Configurações de estoque
- Validade de receitas
- Descontos máximos

---

## 🚀 COMO IMPLEMENTAR

### 1. **Backup do Banco Atual**
```sql
mysqldump -u root -p otica_db > backup_otica_v1.sql
```

### 2. **Criar Novo Banco**
```sql
source otica_db_v2.sql
```

### 3. **Migrar Dados (se necessário)**
- Script de migração pode ser criado conforme necessidade
- Dados podem ser migrados gradualmente

### 4. **Atualizar Configuração**
```php
// config/database.php
define('DB_NAME', 'otica_db_v2');
```

---

## 🔍 DIFERENÇAS DA VERSÃO ANTERIOR

### ✅ **Melhorias Implementadas**
- Estrutura relacional completa
- Controle de estoque automático
- Sistema de receitas expandido
- Ordens de serviço profissionais
- Auditoria completa
- Performance otimizada

### 🔄 **Compatibilidade**
- Mantém funcionalidades existentes
- Adiciona novas funcionalidades
- Permite migração gradual
- Não quebra sistema atual

### 📈 **Escalabilidade**
- Suporte a múltiplos usuários
- Controle de permissões
- Logs de auditoria
- Configurações flexíveis

---

## 🛡️ SEGURANÇA

### Controle de Acesso
- ✅ Perfis de usuário (admin, vendedor, optico, gerente)
- ✅ Senhas hasheadas com bcrypt
- ✅ Controle de sessões
- ✅ Logs de acesso

### Integridade dos Dados
- ✅ Chaves estrangeiras
- ✅ Constraints de validação
- ✅ Transações ACID
- ✅ Backup automático recomendado

---

## 📞 SUPORTE

### Para Implementação
1. Execute o script `otica_db_v2.sql`
2. Configure a conexão no `config/database.php`
3. Teste todas as funcionalidades
4. Migre dados se necessário

### Para Manutenção
- Monitore logs do sistema
- Faça backups regulares
- Atualize índices conforme necessário
- Revise configurações periodicamente

---

## ✅ CONCLUSÃO

O banco de dados V2.0 representa uma evolução significativa do sistema, oferecendo:

- **Estrutura profissional** e escalável
- **Funcionalidades completas** para ótica
- **Performance otimizada** para produção
- **Segurança robusta** para dados sensíveis
- **Flexibilidade** para futuras expansões

O sistema está pronto para uso em produção e pode suportar o crescimento do negócio com confiabilidade e eficiência.
