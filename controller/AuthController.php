<?php
class AuthController extends BaseController {
    private $logger;

    public function __construct($logger) {
        $this->logger = $logger;
        // Assurez-vous que la session est démarrée
        session_start();
    }

    public function register() {
        $errors = [];
        $csrfToken = $this->generateCsrfToken();

        if ($this->isPost()) {
            $this->logger->debug("POST data received: " . json_encode($_POST));
            $postedToken = $this->getPostData()['csrf_token'] ?? '';

            $this->logger->debug("Session CSRF Token: " . ($_SESSION['csrf_token'] ?? 'not set'));
            $this->logger->debug("Posted CSRF Token: " . $postedToken);

            if (!$this->validateCsrfToken($postedToken)) {
                $errors[] = "Jeton CSRF invalide.";
                $this->logger->warning("Invalid CSRF token during registration attempt");
            } else {
                $userData = [
                    'nom' => trim($this->getPostData()['nom'] ?? ''),
                    'prenom' => trim($this->getPostData()['prenom'] ?? ''),
                    'username' => trim($this->getPostData()['username'] ?? ''),
                    'email' => trim($this->getPostData()['email'] ?? ''),
                    'password' => $this->getPostData()['password'] ?? '',
                    'password_confirm' => $this->getPostData()['password_confirm'] ?? '',
                    'adresse' => trim($this->getPostData()['adresse'] ?? ''),
                    'code_postal' => trim($this->getPostData()['code_postal'] ?? ''),
                    'ville' => trim($this->getPostData()['ville'] ?? ''),
                    'telephone' => trim($this->getPostData()['telephone'] ?? '')
                ];

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
                                session_regenerate_id(true);
                                $_SESSION['user_id'] = $user->getId();
                                $this->logger->info("Nouvel utilisateur enregistré: {$userData['username']}");
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

        $this->render('register', ['errors' => $errors, 'csrfToken' => $csrfToken]);
    }

    public function login() {
        $error = null;
        $csrfToken = $this->generateCsrfToken();

        if ($this->isPost()) {
            $this->logger->debug("POST data received: " . json_encode($_POST));
            $username = $this->getPostData()['username'] ?? '';
            $password = $this->getPostData()['password'] ?? '';
            $postedToken = $this->getPostData()['csrf_token'] ?? '';

            $this->logger->debug("Session CSRF Token: " . ($_SESSION['csrf_token'] ?? 'not set'));
            $this->logger->debug("Posted CSRF Token: " . $postedToken);

            if (!$this->validateCsrfToken($postedToken)) {
                $error = "Jeton CSRF invalide.";
                $this->logger->warning("Invalid CSRF token during login attempt");
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

        $this->render('login', ['error' => $error, 'csrfToken' => $csrfToken]);
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
            $this->logger->debug("New CSRF token generated: " . $_SESSION['csrf_token']);
        }
        return $_SESSION['csrf_token'];
    }

    private function validateCsrfToken($token) {
        $isValid = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
        $this->logger->debug("CSRF validation result: " . ($isValid ? "Valid" : "Invalid"));
        return $isValid;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getPostData() {
        return $_POST;
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
}
