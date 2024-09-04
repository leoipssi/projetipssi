<?php
class BaseController {
    protected function render($view, $data = []) {
        extract($data);
        
        ob_start();
        include "views/{$view}.php";
        $content = ob_get_clean();
        
        if (file_exists('views/layouts/main.php')) {
            include 'views/layouts/main.php';
        } else {
            echo $content;
        }
    }
    
    protected function verifyCsrfToken() {
        if ($this->isPost()) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new Exception('Jeton CSRF invalide');
            }
        }
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function redirect($route, $params = []) {
        $url = "index.php?route=" . urlencode($route);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= "&" . urlencode($key) . "=" . urlencode($value);
            }
        }
        header("Location: $url");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
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
        if (!$this->isLoggedIn() || !$this->getCurrentUser()->isAdmin()) {
            $this->redirect('home');
        }
    }
}
