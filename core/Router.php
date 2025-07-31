<?php
/**
 * Classe Router - Gerencia as rotas da aplicação
 */
class Router
{
    private $routes = [];
    private $defaultController = 'Home';
    private $defaultAction = 'index';
    
    public function __construct()
    {
        $this->initRoutes();
    }
    
    /**
     * Inicializa as rotas padrão
     */
    private function initRoutes()
    {
        $this->routes = [
            '' => ['controller' => 'Home', 'action' => 'index'],
            'home' => ['controller' => 'Home', 'action' => 'index'],
            'about' => ['controller' => 'Home', 'action' => 'about'],
            'contact' => ['controller' => 'Home', 'action' => 'contact'],
            'users' => ['controller' => 'User', 'action' => 'index'],
            'users/create' => ['controller' => 'User', 'action' => 'create'],
            'users/edit' => ['controller' => 'User', 'action' => 'edit'],
            'users/delete' => ['controller' => 'User', 'action' => 'delete']
        ];
    }
    
    /**
     * Despacha a requisição para o controller apropriado
     */
    public function dispatch()
    {
        $url = $this->getUrl();
        $route = $this->getRoute($url);
        
        $controllerName = $route['controller'] . 'Controller';
        $actionName = $route['action'] . 'Action';
        
        // Verificar se o controller existe
        if (!class_exists($controllerName)) {
            $this->error404();
            return;
        }
        
        // Instanciar controller
        $controller = new $controllerName();
        
        // Verificar se o método existe
        if (!method_exists($controller, $actionName)) {
            $this->error404();
            return;
        }
        
        // Executar ação
        $controller->$actionName();
    }
    
    /**
     * Obtém a URL atual
     */
    private function getUrl()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url;
    }
    
    /**
     * Obtém a rota baseada na URL
     */
    private function getRoute($url)
    {
        if (isset($this->routes[$url])) {
            return $this->routes[$url];
        }
        
        // Rota padrão
        return [
            'controller' => $this->defaultController,
            'action' => $this->defaultAction
        ];
    }
    
    /**
     * Exibe página de erro 404
     */
    private function error404()
    {
        header("HTTP/1.0 404 Not Found");
        include 'view/error/404.php';
    }
}
?> 