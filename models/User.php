
<?php
require_once __DIR__ . '/../models/Database.php';

class User {
    private $id;
    private $nom;
    private $prenom;
    private $username;
    private $password;
    private $email;
    private $role;
    private $adresse;
    private $code_postal;
    private $ville;
    private $telephone;
    private $created_at;

    private static $logger;

    private static function initLogger() {
        if (self::$logger === null) {
            self::$logger = new class {
                public function debug($message) { error_log("DEBUG: " . $message); }
                public function info($message) { error_log("INFO: " . $message); }
                public function error($message) { error_log("ERROR: " . $message); }
            };
        }
    }

    public function __construct($id, $nom, $prenom, $username, $password, $email, $role, $adresse, $code_postal, $ville, $telephone, $created_at) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
        $this->adresse = $adresse;
        $this->code_postal = $code_postal;
        $this->ville = $ville;
        $this->telephone = $telephone;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getCodePostal() {
        return $this->code_postal;
    }

    public function getVille() {
        return $this->ville;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public static function setLogger($logger) {
        self::initLogger();
        self::$logger = $logger;
    }

    private static function getDB() {
        return Database::getInstance()->getConnection();
    }

    public static function create($userData) {
        self::initLogger();
        
        $db = self::getDB();
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        try {
            self::$logger->debug("Tentative de création d'un nouvel utilisateur: " . $userData['username']);
            $stmt = $db->prepare("INSERT INTO users (nom, prenom, username, password, email, role, adresse, code_postal, ville, telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userData['nom'],
                $userData['prenom'],
                $userData['username'],
                $hashedPassword,
                $userData['email'],
                $userData['role'] ?? 'Utilisateur',
                $userData['adresse'],
                $userData['code_postal'],
                $userData['ville'],
                $userData['telephone']
            ]);
            if ($stmt->rowCount() > 0) {
                self::$logger->info("Nouvel utilisateur créé avec succès: " . $userData['username']);
                return new User(
                    $db->lastInsertId(),
                    $userData['nom'],
                    $userData['prenom'],
                    $userData['username'],
                    $hashedPassword,
                    $userData['email'],
                    $userData['role'] ?? 'Utilisateur',
                    $userData['adresse'],
                    $userData['code_postal'],
                    $userData['ville'],
                    $userData['telephone'],
                    date('Y-m-d H:i:s')
                );
            }
        } catch (PDOException $e) {
            self::$logger->error("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            throw new Exception("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
        }
        return null;
    }

    public static function findById($id) {
        self::initLogger();
        $db = self::getDB();
        try {
            self::$logger->debug("Recherche de l'utilisateur par ID: " . $id);
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = self::createFromRow($stmt->fetch(PDO::FETCH_ASSOC));
            if ($user) {
                self::$logger->debug("Utilisateur trouvé par ID: " . $id);
            } else {
                self::$logger->debug("Aucun utilisateur trouvé pour l'ID: " . $id);
            }
            return $user;
        } catch (PDOException $e) {
            self::$logger->error("Erreur lors de la recherche de l'utilisateur par ID: " . $e->getMessage());
            throw new Exception("Erreur lors de la recherche de l'utilisateur: " . $e->getMessage());
        }
    }

    public static function findByUsername($username) {
        self::initLogger();
        $db = self::getDB();
        try {
            self::$logger->debug("Recherche de l'utilisateur par nom d'utilisateur: " . $username);
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = self::createFromRow($stmt->fetch(PDO::FETCH_ASSOC));
            if ($user) {
                self::$logger->debug("Utilisateur trouvé par nom d'utilisateur: " . $username);
            } else {
                self::$logger->debug("Aucun utilisateur trouvé pour le nom d'utilisateur: " . $username);
            }
            return $user;
        } catch (PDOException $e) {
            self::$logger->error("Erreur lors de la recherche de l'utilisateur par nom d'utilisateur: " . $e->getMessage());
            throw new Exception("Erreur lors de la recherche de l'utilisateur: " . $e->getMessage());
        }
    }

    public static function findByEmail($email) {
        self::initLogger();
        $db = self::getDB();
        try {
            self::$logger->debug("Recherche de l'utilisateur par email: " . $email);
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = self::createFromRow($stmt->fetch(PDO::FETCH_ASSOC));
            if ($user) {
                self::$logger->debug("Utilisateur trouvé par email: " . $email);
            } else {
                self::$logger->debug("Aucun utilisateur trouvé pour l'email: " . $email);
            }
            return $user;
        } catch (PDOException $e) {
            self::$logger->error("Erreur lors de la recherche de l'utilisateur par email: " . $e->getMessage());
            throw new Exception("Erreur lors de la recherche de l'utilisateur: " . $e->getMessage());
        }
    }

    private static function createFromRow($row) {
        if (!$row) return null;
        return new User(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['username'],
            $row['password'],
            $row['email'],
            $row['role'],
            $row['adresse'],
            $row['code_postal'],
            $row['ville'],
            $row['telephone'],
            $row['created_at']
        );
    }

    public static function authenticate($username, $password) {
        self::initLogger();
        self::$logger->debug("Tentative d'authentification pour: " . $username);
        $user = self::findByUsername($username);
        if ($user) {
            self::$logger->debug("Utilisateur trouvé, vérification du mot de passe");
            if (password_verify($password, $user->password)) {
                self::$logger->debug("Mot de passe vérifié avec succès");
                return $user;
            } else {
                self::$logger->debug("Échec de la vérification du mot de passe");
            }
        } else {
            self::$logger->debug("Utilisateur non trouvé");
        }
        return null;
    }

    public static function count() {
        $db = self::getDB();
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM users");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des utilisateurs: " . $e->getMessage());
        }
    }

    public static function getFiltered($page, $search, $role, $sortBy, $sortOrder) {
        $perPage = 10; // Nombre d'utilisateurs par page
        return self::findFiltered($search, $role, $sortBy, $sortOrder, $page, $perPage);
    }

    public static function getTotalPages($search, $role) {
        $perPage = 10; // Même nombre que dans getFiltered
        $totalUsers = self::countFiltered($search, $role);
        return ceil($totalUsers / $perPage);
    }

    public static function findFiltered($search, $role, $sortBy, $sortOrder, $page, $perPage) {
        $db = self::getDB();
        $offset = ($page - 1) * $perPage;
        $allowedSortFields = ['id', 'username', 'email', 'role', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'id';
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        $where = [];
        $params = [];
        if ($search) {
            $where[] = "(username LIKE ? OR email LIKE ? OR nom LIKE ? OR prenom LIKE ?)";
            $searchParam = "%$search%";
            $params = array_fill(0, 4, $searchParam);
        }
        if ($role) {
            $where[] = "role = ?";
            $params[] = $role;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "SELECT * FROM users $whereClause ORDER BY $sortBy $sortOrder LIMIT $perPage OFFSET $offset";

        try {
            $stmt = $db->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = self::createFromRow($row);
            }
            return $users;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la recherche des utilisateurs: " . $e->getMessage());
        }
    }

    public static function countFiltered($search, $role) {
        $db = self::getDB();
        $where = [];
        $params = [];
        if ($search) {
            $where[] = "(username LIKE ? OR email LIKE ? OR nom LIKE ? OR prenom LIKE ?)";
            $searchParam = "%$search%";
            $params = array_fill(0, 4, $searchParam);
        }
        if ($role) {
            $where[] = "role = ?";
            $params[] = $role;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "SELECT COUNT(*) FROM users $whereClause";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des utilisateurs: " . $e->getMessage());
        }
    }

    public function hasPermission($action) {
        switch ($this->role) {
            case 'Administrateur':
                return true;
            case 'Utilisateur':
                return in_array($action, ['view', 'rent']);
            default:
                return $action === 'view';
        }
    }

    public function update($data) {
        $db = self::getDB();
        $allowedFields = ['nom', 'prenom', 'username', 'email', 'role', 'adresse', 'code_postal', 'ville', 'telephone'];
        $updates = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "{$key} = ?";
                $values[] = $value;
                $this->$key = $value;
            }
        }

        if (!empty($updates)) {
            $values[] = $this->id;
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            try {
                $stmt = $db->prepare($sql);
                return $stmt->execute($values);
            } catch (PDOException $e) {
                throw new Exception("Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage());
            }
        }

        return false;
    }

    public function changePassword($newPassword) {
        $db = self::getDB();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $stmt->execute([$hashedPassword, $this->id]);
            if ($result) {
                $this->password = $hashedPassword;
                return true;
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du changement de mot de passe: " . $e->getMessage());
        }
        return false;
    }

    public static function getAvailableRoles() {
        return ['Administrateur', 'Utilisateur', 'Gestionnaire'];
    }

    public function isAdmin() {
        return $this->role === 'Administrateur';
    }

    public static function findAllClients() {
        $db = self::getDB();
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE role = 'Utilisateur'");
            $stmt->execute();
            $clients = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $clients[] = self::createFromRow($row);
            }
            return $clients;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des clients: " . $e->getMessage());
        }
    }
}
