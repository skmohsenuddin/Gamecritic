<?php
class Router {
    private $routes = [];

    public function addRoute($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($path, $controller, $action) {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post($path, $controller, $action) {
        $this->addRoute('POST', $path, $controller, $action);
    }

    public function dispatch($requestMethod, $requestUri) {
        $uri = parse_url($requestUri, PHP_URL_PATH);

        if ($uri === false || $uri === null) {
            $uri = '/';
        }
        if ($uri === '') {
            $uri = '/';
        }
        if ($uri[0] !== '/') {
            $uri = '/' . ltrim($uri, '/');
        }

        if (strpos($uri, '/Gamecritic') === 0) {
            $uri = substr($uri, strlen('/Gamecritic'));
        }
        
        if (strpos($uri, '/public') === 0) {
            $uri = substr($uri, strlen('/public'));
        }

        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        if ($uri === '/index.php' || $uri === '/public/index.php') {
            $uri = '/';
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchPath($route['path'], $uri)) {
                $params = $this->extractParams($route['path'], $uri);
                return $this->executeController($route['controller'], $route['action'], $params);
            }
        }
        
        http_response_code(404);
        echo "<!DOCTYPE html>
<html>
<head>
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; text-align: center; }
        .error-box { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        h1 { color: #d32f2f; }
        .info { text-align: left; margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 3px; }
        .routes { text-align: left; margin-top: 20px; }
        .routes ul { list-style: none; padding: 0; }
        .routes li { padding: 5px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class='error-box'>
        <h1>404 - Page Not Found</h1>
        <div class='info'>
            <strong>Requested:</strong> {$requestMethod} {$uri}<br>
            <strong>Original URI:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') . "<br>
            <strong>Base URL:</strong> Make sure you're accessing: <code>http://localhost:881/Gamecritic/public/</code>
        </div>
        <div class='routes'>
            <h3>Available Routes:</h3>
            <ul>";
        foreach ($this->routes as $route) {
            echo "<li><strong>{$route['method']}</strong> {$route['path']}</li>";
        }
        echo "</ul>
        </div>
        <p><a href='/Gamecritic/public/'>← Go to Homepage</a></p>
    </div>
</body>
</html>";
    }

    private function matchPath($routePath, $requestPath) {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $requestPath);
    }

    private function extractParams($routePath, $requestPath) {
        $params = [];
        
        preg_match_all('/\{([^}]+)\}/', $routePath, $paramNames);
        
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        preg_match($pattern, $requestPath, $paramValues);
        
        for ($i = 0; $i < count($paramNames[1]); $i++) {
            $params[$paramNames[1][$i]] = $paramValues[$i + 1];
        }
        
        return $params;
    }

    private function executeController($controllerName, $actionName, $params = []) {
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}Controller.php";
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: {$controllerFile}");
        }
        
        require_once $controllerFile;
        
        $controllerClass = $controllerName . 'Controller';
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $actionName)) {
            throw new Exception("Action not found: {$actionName} in {$controllerClass}");
        }
        
        return call_user_func_array([$controller, $actionName], $params);
    }
}
?>


