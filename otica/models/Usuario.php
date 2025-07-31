<?php
/**
 * Model Usuario - Gerenciamento de usuários
 */
class Usuario extends BaseModel
{
    protected $table = 'usuarios';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Cria um novo usuário com hash da senha
     */
    public function create($data)
    {
        // Hash da senha
        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        
        // Adicionar data de criação
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return parent::create($data);
    }
    
    /**
     * Atualiza um usuário
     */
    public function update($data)
    {
        // Hash da senha se fornecida
        if (isset($data['senha']) && !empty($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        } else {
            unset($data['senha']);
        }
        
        // Adicionar data de atualização
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return parent::update($data);
    }
    
    /**
     * Busca usuário por email
     */
    public function getByEmail($email)
    {
        return $this->find(['email' => $email], null, 1);
    }
    
    /**
     * Verifica se email já existe
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    /**
     * Autentica usuário
     */
    public function authenticate($email, $senha)
    {
        $usuario = $this->getByEmail($email);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        
        return false;
    }
    
    /**
     * Busca usuários por perfil
     */
    public function getByPerfil($perfil)
    {
        return $this->find(['perfil' => $perfil]);
    }
    
    /**
     * Busca usuários ativos
     */
    public function getAtivos()
    {
        return $this->find(['ativo' => 1]);
    }
    
    /**
     * Atualiza último login
     */
    public function updateUltimoLogin($id)
    {
        $sql = "UPDATE {$this->table} SET ultimo_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Valida dados do usuário
     */
    public function validate($data, $excludeId = null)
    {
        $errors = [];
        
        // Validar nome
        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        
        // Validar email
        if (empty($data['email'])) {
            $errors['email'] = 'Email é obrigatório';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } elseif ($this->emailExists($data['email'], $excludeId)) {
            $errors['email'] = 'Email já existe';
        }
        
        // Validar senha (apenas para criação)
        if (!$excludeId && empty($data['senha'])) {
            $errors['senha'] = 'Senha é obrigatória';
        }
        
        // Validar perfil
        if (empty($data['perfil'])) {
            $errors['perfil'] = 'Perfil é obrigatório';
        }
        
        return $errors;
    }
}
?> 