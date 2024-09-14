<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

class AuthController extends BaseController {
    public function __construct($logger = null) {
        parent::__construct($logger);
        User::setLogger($this->logger);
    }

    public function register() {
        error_log("Début de la méthode register");
        $errors = [];
        $csrfToken = $this->csrf_token();
        error_log("Generated CSRF Token for registration: " . $csrfToken);
        if ($this->isPost()) {
            error_log("POST request detected for registration");
            error_log("POST data received for registration: " . json_encode($_POST));
            try {
                $this->verifyCsrfToken();
                error_log("CSRF verification passed for registration");
                $userData = $this->sanitizeUserData($_POST);
                $errors = $this->validateRegistrationInput($userData);
                if (empty($errors)) {
                    try {
                        if (User::findByUsername($userData['username'])) {
                            $errors[] = "Ce nom d'utilisateur est déjà pris.";
                        } elseif (User::findByEmail($userData['email'])) {
                            $errors[] = "Cette adresse email est déjà utilisée.";
                        } else {
                            $user = User::create($userData);
                            if ($user) {
                                $this->initializeUserSession($user);
                                error_log("Nouvel utilisateur enregistré: {$userData['username']}");
                                $this->redirect('home');
                            } else {
                                $errors[] = "Erreur lors de l'inscription.";
                            }
                        }
                    } catch (Exception $e) {
                        error_log("Erreur lors de l'inscription: " . $e->getMessage());
                        $errors[] = "Une erreur est survenue lors de l'inscription.";
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Jeton CSRF invalide : " . $e->getMessage();
                error_log("CSRF verification failed for registration: " . $e->getMessage());
            }
        }
        error_log("Rendering registration view with CSRF token: " . $csrfToken);
        $this->render('auth/register', ['errors' => $errors, 'csrfToken' => $csrfToken]);
    }

    public function login() {
        error_log("Début de la méthode login");
        $error = null;
        $csrfToken = $this->csrf_token();
        error_log("Generated CSRF Token for login: " . $csrfToken);
        if ($this->isPost()) {
            error_log("POST request detected for login");
            error_log("POST data received for login: " . json_encode($_POST));
            
            try {
                $this->verifyCsrfToken();
                error_log("CSRF verification passed for login");
                $username = $this->getPostData()['username'] ?? '';
                $password = $this->getPostData()['password'] ?? '';
                error_log("Attempting authentication for user: " . $username);
                try {
                    $user = User::authenticate($username, $password);
                    if ($user) {
                        $this->initializeUserSession($user);
                        error_log("Connexion réussie: {$username}");
                        error_log("Redirecting to home page after successful login");
                        $this->redirect('home');
                    } else {
                        error_log("Tentative de connexion échouée pour l'utilisateur: {$username}");
                        $error = "Nom d'utilisateur ou mot de passe incorrect.";
                    }
                } catch (Exception $e) {
                    error_log("Erreur détaillée lors de la connexion: " . $e->getMessage());
                    $error = "Une erreur est survenue lors de la connexion.";
                }
            } catch (Exception $e) {
                $error = "Jeton CSRF invalide : " . $e->getMessage();
                error_log("CSRF verification failed for login: " . $e->getMessage());
            }
        }
        error_log("Rendering login view with CSRF token: " . $csrfToken);
        $this->render('auth/login', ['error' => $error, 'csrfToken' => $csrfToken]);
    }

    public function logout() {
        $userId = $_SESSION['user_id'] ?? null;
        session_unset();
        session_destroy();
        if ($userId) {
            error_log("Utilisateur déconnecté: ID {$userId}");
        }
        $this->redirect('home');
    }

    protected function validateRegistrationInput($data) {
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

    protected function initializeUserSession($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();
        $_SESSION['is_admin'] = $user->isAdmin();
        error_log("User session initialized. User ID: {$user->getId()}, Role: {$user->getRole()}, Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No'));
    }
}
