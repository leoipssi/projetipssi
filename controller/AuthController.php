<?php
class AuthController {
    private $logger;

    public function __construct($logger) {
        $this->logger = $logger;
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $csrfToken = $_POST['csrf_token'] ?? '';
            
            if (!$this->validateCsrfToken($csrfToken)) {
                $errors[] = "Jeton CSRF invalide.";
            } else {
                $errors = $this->validateRegistrationInput($username, $email, $password);
                
                if (empty($errors)) {
                    try {
                        if (User::findByUsername($username)) {
                            $errors[] = "Ce nom d'utilisateur est déjà pris.";
                        } elseif (User::findByEmail($email)) {
                            $errors[] = "Cette adresse email est déjà utilisée.";
                        } else {
                            $user = User::create($username, $password, $email);
                            if ($user) {
                                session_regenerate_id(true);
                                $_SESSION['user_id'] = $user->getId();
                                $this->logger->info("Nouvel utilisateur enregistré: {$username}");
                                header('Location: index.php');
                                exit;
                            } else {
                                $errors[] = "Erreur lors de l'inscription.";
                            }
                        }
                    } catch (Exception $e) {
                        $this->logger->error("Erreur lors de l'inscription: " . $e->getMessage());
                        $errors[] = "Une erreur est survenue lors de l'inscription.";
                    }
                }
            }
        }
        $csrfToken = $this->generateCsrfToken();
        $content = $this->render('register', ['errors' => $errors ?? null, 'csrfToken' => $csrfToken]);
        $this->renderLayout($content);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $csrfToken = $_POST['csrf_token'] ?? '';
            
            if (!$this->validateCsrfToken($csrfToken)) {
                $error = "Jeton CSRF invalide.";
            } else {
                try {
                    $user = User::authenticate($username, $password);
                    if ($user) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user->getId();
                        $this->logger->info("Connexion réussie: {$username}");
                        header('Location: index.php');
                        exit;
                    } else {
                        $this->logger->warning("Tentative de connexion échouée pour l'utilisateur: {$username}");
                        $error = "Nom d'utilisateur ou mot de passe incorrect.";
                    }
                } catch (Exception $e) {
                    $this->logger->error("Erreur lors de la connexion: " . $e->getMessage());
                    $error = "Une erreur est survenue lors de la connexion.";
                }
            }
        }
        $csrfToken = $this->generateCsrfToken();
        $content = $this->render('login', ['error' => $error ?? null, 'csrfToken' => $csrfToken]);
        $this->renderLayout($content);
    }

    public function logout() {
        $userId = $_SESSION['user_id'] ?? null;
        session_unset();
        session_destroy();
        if ($userId) {
            $this->logger->info("Utilisateur déconnecté: ID {$userId}");
        }
        header('Location: index.php');
        exit;
    }

    private function validateRegistrationInput($username, $email, $password) {
        $errors = [];
        if (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Le nom d'utilisateur doit contenir entre 3 et 50 caractères.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        return $errors;
    }

    private function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    private function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
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
