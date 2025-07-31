<?php
/**
 * Controller Home - Página inicial
 */
class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Página inicial
     */
    public function indexAction()
    {
        $data = [
            'title' => 'Página Inicial',
            'message' => 'Bem-vindo à aplicação MVC!'
        ];
        
        $this->render('home/index', $data);
    }
    
    /**
     * Página sobre
     */
    public function aboutAction()
    {
        $data = [
            'title' => 'Sobre',
            'content' => 'Esta é uma aplicação MVC desenvolvida em PHP.'
        ];
        
        $this->render('home/about', $data);
    }
    
    /**
     * Página de contato
     */
    public function contactAction()
    {
        if ($this->isPost()) {
            // Processar formulário de contato
            $name = $this->getPost('name');
            $email = $this->getPost('email');
            $message = $this->getPost('message');
            
            // Aqui você pode adicionar validação e envio de email
            
            $data = [
                'title' => 'Contato',
                'success' => 'Mensagem enviada com sucesso!'
            ];
        } else {
            $data = [
                'title' => 'Contato',
                'success' => null
            ];
        }
        
        $this->render('home/contact', $data);
    }
}
?> 