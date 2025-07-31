<?php
/**
 * Model User - Gerenciamento de usuários
 */
class UserModel extends BaseModel
{
    protected $table = 'users';
    
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
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
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
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
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
     * Valida dados do usuário
     */
    public function validate($data, $excludeId = null)
    {
        $errors = [];
        
        // Validar nome
        if (empty($data['name'])) {
            $errors['name'] = 'Nome é obrigatório';
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
        if (!$excludeId && empty($data['password'])) {
            $errors['password'] = 'Senha é obrigatória';
        }
        
        return $errors;
    }
}
?> 