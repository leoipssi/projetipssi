<?php
class AuthController {
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            if (User::findByUsername($username)) {
                $errors[] = "Ce nom d'utilisateur est déjà pris.";
            } else {
                $user = User::create($username, $password, $email);
                if ($user) {
                    $_SESSION['user_id'] = $user->getId();
                    header('Location: index.php');
                    exit;
                } else {
                    $errors[] = "Erreur lors de l'inscription.";
                }
            }
        }
        $content = $this->render('register', ['errors' => $errors ?? null]);
        $this->renderLayout($content);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = User::authenticate($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user->getId();
                header('Location: index.php');
                exit;
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect.";
            }
        }
        $content = $this->render('login', ['error' => $error ?? null]);
        $this->renderLayout($content);
    }

    public function logout() {
        unset($_SESSION['user_id']);
        header('Location: index.php');
        exit;
    }

    private function render($view, $data = []) {
        extract($data);
        ob_start();
        include "views/{$view}.php";
        return ob_get_clean();
    }

    private function renderLayout($content) {
        include 'views/layouts/main.php';
    }
}
