<?php
/**
 * Controller Login - Gerenciamento de autenticação
 */
class LoginController extends Controller
{
    private $usuarioModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = $this->loadModel('Usuario');
    }
    
    /**
     * Exibe formulário de login
     */
    public function indexAction()
    {
        // Se já está logado, redireciona para dashboard
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }
        
        if ($this->isPost()) {
            $email = $this->getPost('email');
            $senha = $this->getPost('senha');
            
            // Autenticar usuário
            $usuario = $this->usuarioModel->authenticate($email, $senha);
            
            if ($usuario) {
                // Criar sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];
                
                // Atualizar último login
                $this->usuarioModel->updateUltimoLogin($usuario['id']);
                
                $this->redirect('dashboard');
            } else {
                $data = [
                    'title' => 'Login',
                    'error' => 'Email ou senha inválidos'
                ];
                $this->render('login/index', $data);
            }
        } else {
            $data = [
                'title' => 'Login'
            ];
            $this->render('login/index', $data);
        }
    }
    
    /**
     * Logout
     */
    public function logoutAction()
    {
        // Destruir sessão
        session_destroy();
        
        $this->redirect('login');
    }
    
    /**
     * Exibe formulário de cadastro
     */
    public function cadastroAction()
    {
        if ($this->isPost()) {
            $usuarioData = [
                'nome' => $this->getPost('nome'),
                'email' => $this->getPost('email'),
                'senha' => $this->getPost('senha'),
                'perfil' => $this->getPost('perfil', 'usuario'),
                'ativo' => 1
            ];
            
            // Validar dados
            $errors = $this->usuarioModel->validate($usuarioData);
            
            if (empty($errors)) {
                if ($this->usuarioModel->create($usuarioData)) {
                    $data = [
                        'title' => 'Cadastro',
                        'success' => 'Usuário cadastrado com sucesso! Faça login para continuar.'
                    ];
                    $this->render('login/index', $data);
                } else {
                    $data = [
                        'title' => 'Cadastro',
                        'error' => 'Erro ao cadastrar usuário',
                        'usuario' => $usuarioData
                    ];
                    $this->render('login/cadastro', $data);
                }
            } else {
                $data = [
                    'title' => 'Cadastro',
                    'errors' => $errors,
                    'usuario' => $usuarioData
                ];
                $this->render('login/cadastro', $data);
            }
        } else {
            $data = [
                'title' => 'Cadastro'
            ];
            $this->render('login/cadastro', $data);
        }
    }
    
    /**
     * Exibe formulário de recuperação de senha
     */
    public function recuperarSenhaAction()
    {
        if ($this->isPost()) {
            $email = $this->getPost('email');
            
            // Verificar se email existe
            $usuario = $this->usuarioModel->getByEmail($email);
            
            if ($usuario) {
                // Gerar token de recuperação
                $token = bin2hex(random_bytes(32));
                
                // Aqui você pode implementar o envio de email
                // com o link para redefinir a senha
                
                $data = [
                    'title' => 'Recuperar Senha',
                    'success' => 'Email de recuperação enviado com sucesso!'
                ];
                $this->render('login/recuperar_senha', $data);
            } else {
                $data = [
                    'title' => 'Recuperar Senha',
                    'error' => 'Email não encontrado'
                ];
                $this->render('login/recuperar_senha', $data);
            }
        } else {
            $data = [
                'title' => 'Recuperar Senha'
            ];
            $this->render('login/recuperar_senha', $data);
        }
    }
    
    /**
     * Redefine senha
     */
    public function redefinirSenhaAction()
    {
        $token = $this->getGet('token');
        
        if ($this->isPost()) {
            $senha = $this->getPost('senha');
            $confirmarSenha = $this->getPost('confirmar_senha');
            
            if ($senha === $confirmarSenha) {
                // Aqui você pode implementar a validação do token
                // e atualização da senha
                
                $data = [
                    'title' => 'Redefinir Senha',
                    'success' => 'Senha redefinida com sucesso!'
                ];
                $this->render('login/index', $data);
            } else {
                $data = [
                    'title' => 'Redefinir Senha',
                    'error' => 'Senhas não coincidem'
                ];
                $this->render('login/redefinir_senha', $data);
            }
        } else {
            $data = [
                'title' => 'Redefinir Senha',
                'token' => $token
            ];
            $this->render('login/redefinir_senha', $data);
        }
    }
    
    /**
     * Verifica se usuário está logado
     */
    public function verificarLoginAction()
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->json(['logado' => true, 'usuario' => $_SESSION]);
        } else {
            $this->json(['logado' => false]);
        }
    }
}
?> 