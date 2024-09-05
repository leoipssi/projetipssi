<?php
class AuthController extends BaseController {
    public function __construct($logger = null) {
        parent::__construct($logger);
    }

    public function register() {
        $errors = [];
        $csrfToken = $this->generateCsrfToken();

        if ($this->isPost()) {
            try {
                $this->verifyCsrfToken();

                $userData = $this->sanitizeUserData($_POST);
                $errors = $this->validateRegistrationInput($userData);

                if (empty($errors)) {
                    if (User::findByUsername($userData['username'])) {
                        $errors[] = "Ce nom d'utilisateur est déjà pris.";
                    } elseif (User::findByEmail($userData['email'])) {
                        $errors[] = "Cette adresse email est déjà utilisée.";
                    } else {
                        $user = User::create($userData);
                        if ($user) {
                            $this->initializeUserSession($user);
                            $this->redirect('home');
                        } else {
                            $errors[] = "Erreur lors de l'inscription.";
                        }
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Jeton CSRF invalide : " . $e->getMessage();
            }
        }

        $this->render('auth/register', ['errors' => $errors, 'csrfToken' => $csrfToken]);
    }

    public function login() {
        $error = null;
        $csrfToken = $this->generateCsrfToken();

        if ($this->isPost()) {
            try {
                $this->verifyCsrfToken();

                $username = $this->getPostData()['username'] ?? '';
                $password = $this->getPostData()['password'] ?? '';

                $user = User::authenticate($username, $password);
                if ($user) {
                    $this->initializeUserSession($user);
                    $this->redirect('home');
                } else {
                    $error = "Nom d'utilisateur ou mot de passe incorrect.";
                }
            } catch (Exception $e) {
                $error = "Jeton CSRF invalide : " . $e->getMessage();
            }
        }

        $this->render('auth/login', ['error' => $error, 'csrfToken' => $csrfToken]);
    }

    public function logout() {
        $userId = $_SESSION['user_id'] ?? null;
        session_unset();
        session_destroy();
        if ($userId) {
            $this->logger->info("Utilisateur déconnecté: ID {$userId}");
        }
        $this->redirect('home');
    }

    public static function isAdmin() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function checkLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    private function sanitizeUserData($data) {
        return array_map('trim', array_map('htmlspecialchars', $data));
    }

    private function validateRegistrationInput($data) {
        $errors = [];
        if (strlen($data['username']) < 3 || strlen($data['username']) > 50) {
            $errors[] = "Le nom d'utilisateur doit contenir entre 3 et 50 caractères.";
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }
        if (strlen($data['password']) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (strlen($data['nom']) < 2 || strlen($data['prenom']) < 2) {
            $errors[] = "Le nom et le prénom doivent contenir au moins 2 caractères.";
        }
        if (strlen($data['adresse']) < 5) {
            $errors[] = "L'adresse doit contenir au moins 5 caractères.";
        }
        if (!preg_match("/^\d{5}$/", $data['code_postal'])) {
            $errors[] = "Le code postal doit contenir 5 chiffres.";
        }
        if (strlen($data['ville']) < 2) {
            $errors[] = "La ville doit contenir au moins 2 caractères.";
        }
        if (!preg_match("/^[0-9]{10}$/", $data['telephone'])) {
            $errors[] = "Le numéro de téléphone doit contenir 10 chiffres.";
        }
        return $errors;
    }

    private function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfToken() {
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Jeton CSRF invalide.");
        }
    }

    private function initializeUserSession($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
    }
}
