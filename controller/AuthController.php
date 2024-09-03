<?php
class AuthController extends BaseController {
    private $logger;

    // Constructeur avec le logger en paramètre
    public function __construct($logger) {
        $this->logger = $logger;
    }

    // Méthode pour l'inscription des utilisateurs
    public function register() {
        $errors = [];
        if ($this->isPost()) {
            $username = trim($this->getPostData()['username']);
            $email = trim($this->getPostData()['email']);
            $password = $this->getPostData()['password'];
            $csrfToken = $this->getPostData()['csrf_token'] ?? '';
            
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
                                $this->redirect('home');
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
        $this->render('register', ['errors' => $errors ?? null, 'csrfToken' => $csrfToken]);
    }

    // Méthode pour la connexion des utilisateurs
    public function login() {
        $error = null;
        if ($this->isPost()) {
            $username = $this->getPostData()['username'];
            $password = $this->getPostData()['password'];
            $csrfToken = $this->getPostData()['csrf_token'] ?? '';
            
            if (!$this->validateCsrfToken($csrfToken)) {
                $error = "Jeton CSRF invalide.";
            } else {
                try {
                    $user = User::authenticate($username, $password);
                    if ($user) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user->getId();
                        $this->logger->info("Connexion réussie: {$username}");
                        $this->redirect('home');
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
        $this->render('login', ['error' => $error ?? null, 'csrfToken' => $csrfToken]);
    }

    // Méthode pour la déconnexion des utilisateurs
    public function logout() {
        $userId = $_SESSION['user_id'] ?? null;
        session_unset();
        session_destroy();
        if ($userId) {
            $this->logger->info("Utilisateur déconnecté: ID {$userId}");
        }
        $this->redirect('home');
    }

    // Validation des données d'inscription
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

    // Génération du jeton CSRF
    private function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    // Validation du jeton CSRF
    private function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
