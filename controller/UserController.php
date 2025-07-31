<?php
/**
 * Controller User - Gerenciamento de usuários
 */
class UserController extends Controller
{
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = $this->loadModel('User');
    }
    
    /**
     * Lista todos os usuários
     */
    public function indexAction()
    {
        $users = $this->userModel->getAll();
        
        $data = [
            'title' => 'Usuários',
            'users' => $users
        ];
        
        $this->render('user/index', $data);
    }
    
    /**
     * Exibe formulário para criar usuário
     */
    public function createAction()
    {
        if ($this->isPost()) {
            $userData = [
                'name' => $this->getPost('name'),
                'email' => $this->getPost('email'),
                'password' => $this->getPost('password')
            ];
            
            if ($this->userModel->create($userData)) {
                $this->redirect('users');
            } else {
                $data = [
                    'title' => 'Criar Usuário',
                    'error' => 'Erro ao criar usuário',
                    'user' => $userData
                ];
                $this->render('user/create', $data);
            }
        } else {
            $data = [
                'title' => 'Criar Usuário'
            ];
            $this->render('user/create', $data);
        }
    }
    
    /**
     * Exibe formulário para editar usuário
     */
    public function editAction()
    {
        $id = $this->getGet('id');
        
        if ($this->isPost()) {
            $userData = [
                'id' => $id,
                'name' => $this->getPost('name'),
                'email' => $this->getPost('email')
            ];
            
            if ($this->userModel->update($userData)) {
                $this->redirect('users');
            } else {
                $data = [
                    'title' => 'Editar Usuário',
                    'error' => 'Erro ao atualizar usuário',
                    'user' => $userData
                ];
                $this->render('user/edit', $data);
            }
        } else {
            $user = $this->userModel->getById($id);
            
            $data = [
                'title' => 'Editar Usuário',
                'user' => $user
            ];
            $this->render('user/edit', $data);
        }
    }
    
    /**
     * Remove um usuário
     */
    public function deleteAction()
    {
        $id = $this->getGet('id');
        
        if ($this->userModel->delete($id)) {
            $this->redirect('users');
        } else {
            $this->redirect('users');
        }
    }
}
?> 