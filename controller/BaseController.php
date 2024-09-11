<?php
class BaseController {
    protected $logger;

    public function __construct($logger = null) {
        if ($logger === null) {
            $this->logger = new \Monolog\Logger('base');
            $this->logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/base.log', \Monolog\Logger::DEBUG));
        } else {
            $this->logger = $logger;
        }
        $this->logger->info('BaseController initialized');

        if (!defined('BASE_URL')) {
            $this->logger->error('BASE_URL is not defined');
            die('Configuration error: BASE_URL is not defined');
        }
        
        $viewsPath = __DIR__ . "/../views";
        $layoutsPath = __DIR__ . "/../views/layouts";
        
        if (!is_dir($viewsPath)) {
            $this->logger->error('Views directory not found: ' . $viewsPath);
            die('Configuration error: Views directory not found');
        }
        
        if (!is_dir($layoutsPath)) {
            $this->logger->error('Layouts directory not found: ' . $layoutsPath);
            die('Configuration error: Layouts directory not found');
        }
    }

    protected function render($view, $data = [], $layout = 'main') {
        try {
            $this->logger->info("Rendering view: $view");
            extract($data);
            
            ob_start();  
            $viewPath = __DIR__ . "/../views/{$view}.php";
            if (!file_exists($viewPath)) {
                throw new Exception("Vue non trouvée : {$view}");
            }
            include $viewPath;
            $content = ob_get_clean(); 
            
            if ($layout && file_exists(__DIR__ . "/../layouts/{$layout}.php")) {
                include __DIR__ . "/../layouts/{$layout}.php";
            } else {
                echo $content;
            }
            $this->logger->info("View rendered successfully: $view");
        } catch (Exception $e) {
            $this->logger->error('Erreur lors du rendu de la vue: ' . $e->getMessage());
            echo "Une erreur est survenue lors de l'affichage de la page. Détails : " . $e->getMessage();
            echo "<br>Fichier : " . $e->getFile() . " à la ligne " . $e->getLine();
            echo "<br>Trace : <pre>" . $e->getTraceAsString() . "</pre>";
        }
    }

    protected function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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

    protected function asset($path) {
        return BASE_URL . '/public/' . $path;
    }

    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Administrateur';
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function redirect($route, $params = []) {
        $url = $this->url($route, $params);
        header("Location: $url");
        exit;
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

    public function isLoggedIn() {
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

    protected function renderError($code, $message = null) {
        http_response_code($code);
        $this->render("errors/{$code}", [
            'title' => "Erreur {$code}",
            'message' => $message
        ], 'main');
    }

    protected function sanitizeUserData($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }

    protected function log($level, $message, array $context = []) {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    protected function csrf_token() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
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

    protected function debugInfo() {
        echo "<pre>";
        echo "Current PHP version: " . phpversion() . "\n";
        echo "Loaded PHP extensions: " . implode(', ', get_loaded_extensions()) . "\n";
        echo "Include path: " . get_include_path() . "\n";
        echo "Current working directory: " . getcwd() . "\n";
        echo "Script filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
        echo "</pre>";
    }
}
?>
