<?php
/**
 * Classe base Controller
 */
abstract class Controller
{
    protected $model;
    protected $view;
    
    public function __construct()
    {
        // Inicializar sessão
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Carrega um modelo
     */
    protected function loadModel($modelName)
    {
        $modelFile = 'model/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $modelClass = $modelName . 'Model';
            return new $modelClass();
        }
        return null;
    }
    
    /**
     * Renderiza uma view
     */
    protected function render($view, $data = [])
    {
        // Extrair dados para variáveis
        extract($data);
        
        // Incluir view
        $viewFile = 'view/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View não encontrada: " . $viewFile);
        }
    }
    
    /**
     * Redireciona para outra URL
     */
    protected function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }
    
    /**
     * Retorna resposta JSON
     */
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Verifica se é uma requisição POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verifica se é uma requisição GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Obtém dados POST
     */
    protected function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Obtém dados GET
     */
    protected function getGet($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
}
?> 