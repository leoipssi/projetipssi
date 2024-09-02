<?php
class AuthController {
    public function showLoginForm() {
        $content = $this->render('login');
        $this->renderLayout($content);
    }

    public function login($username, $password) {
        $user = User::authenticate($username, $password);
        if ($user) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_role'] = $user->getRole();
            header('Location: index.php');
        } else {
            $error = "Identifiants incorrects";
            $content = $this->render('login', ['error' => $error]);
            $this->renderLayout($content);
        }
    }

    public function showRegisterForm() {
        $content = $this->render('register');
        $this->renderLayout($content);
    }

    public function register($data) {
        $errors = $this->validateRegistrationData($data);
        if (empty($errors)) {
            $user = User::create($data);
            if ($user) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_role'] = $user->getRole();
                header('Location: index.php');
            } else {
                $errors[] = "Erreur lors de la création du compte";
            }
        }
        if (!empty($errors)) {
            $content = $this->render('register', ['errors' => $errors, 'data' => $data]);
            $this->renderLayout($content);
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    private function validateRegistrationData($data) {
        $errors = [];
        if (empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis";
        }
        if (empty($data['email'])) {
            $errors[] = "L'email est requis";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email n'est pas valide";
        }
        if (empty($data['password'])) {
            $errors[] = "Le mot de passe est requis";
        } elseif (strlen($data['password']) < 8) {
            $errors[] = "Le mot de passe doit faire au moins 8 caractères";
        }
        return $errors;
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
