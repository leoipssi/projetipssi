<?php
class BaseController {
    protected $logger;

    public function __construct($logger = null) {
        if ($logger === null) {
            // Créer un logger par défaut si aucun n'est fourni
            $this->logger = new \Monolog\Logger('base');
            $this->logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/base.log', \Monolog\Logger::DEBUG));
        } else {
            $this->logger = $logger;
        }
    }

    protected function render($view, $data = [], $layout = 'main') {
        extract($data);
        
        ob_start();
        include "views/{$view}.php";
        $content = ob_get_clean();
        
        if ($layout && file_exists("views/layouts/{$layout}.php")) {
            include "views/layouts/{$layout}.php";
        } else {
            echo $content;
        }
    }
    
    protected function verifyCsrfToken() {
        if ($this->isPost()) {
            if (!isset($_POST['csrf_token'])) {
                throw new Exception('Jeton CSRF manquant dans la requête POST');
            }
            if (!isset($_SESSION['csrf_token'])) {
                throw new Exception('Jeton CSRF manquant dans la session');
            }
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Les jetons CSRF ne correspondent pas');
            }
        }
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function redirect($route, $params = []) {
        $url = $this->url($route, $params);
        header("Location: $url");
        exit;
    }

    protected function url($route, $params = []) {
        $url = BASE_URL . "/index.php?route=" . urlencode($route);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= "&" . urlencode($key) . "=" . urlencode($value);
            }
        }
        return $url;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function getPostData() {
        return $_POST;
    }

    protected function getQueryParam($key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    protected function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return User::findById($_SESSION['user_id']);
        }
        return null;
    }

    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }

    protected function requireAdmin() {
        $user = $this->getCurrentUser();
        if (!$this->isLoggedIn() || !$user || !$user->isAdmin()) {
            $this->redirect('home');
        }
    }

    protected function renderError($code) {
        http_response_code($code);
        $this->render("errors/{$code}", ['title' => "Erreur {$code}"], 'main');
    }
}
