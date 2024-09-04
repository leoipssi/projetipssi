<?php

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
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getAdresse() { return $this->adresse; }
    public function getCodePostal() { return $this->code_postal; }
    public function getVille() { return $this->ville; }
    public function getTelephone() { return $this->telephone; }
    public function getCreatedAt() { return $this->created_at; }

    public static function create($userData) {
        global $conn;
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare("INSERT INTO users (nom, prenom, username, password, email, role, adresse, code_postal, ville, telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
                return new User(
                    $conn->lastInsertId(),
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
            error_log("Erreur lors de la création de l'utilisateur : " . $e->getMessage());
            throw new Exception("Erreur lors de la création de l'utilisateur.");
        }
        return null;
    }

    public static function findById($id) {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur par ID : " . $e->getMessage());
            throw new Exception("Erreur lors de la recherche de l'utilisateur.");
        }
        return null;
    }

    public static function findByUsername($username) {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur par nom d'utilisateur : " . $e->getMessage());
            throw new Exception("Erreur lors de la recherche de l'utilisateur.");
        }
        return null;
    }

    public static function findByEmail($email) {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'utilisateur par email : " . $e->getMessage());
            throw new Exception("Erreur lors de la recherche de l'utilisateur.");
        }
        return null;
    }

    public static function authenticate($username, $password) {
        $user = self::findByUsername($username);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }

    public static function count() {
        global $conn;
        try {
            $stmt = $conn->query("SELECT COUNT(*) FROM users");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des utilisateurs : " . $e->getMessage());
            throw new Exception("Erreur lors du comptage des utilisateurs.");
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
        global $conn;
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
                $stmt = $conn->prepare($sql);
                return $stmt->execute($values);
            } catch (PDOException $e) {
                error_log("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
                throw new Exception("Erreur lors de la mise à jour de l'utilisateur.");
            }
        }

        return false;
    }

    public function changePassword($newPassword) {
        global $conn;
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $stmt->execute([$hashedPassword, $this->id]);
            if ($result) {
                $this->password = $hashedPassword;
                return true;
            }
        } catch (PDOException $e) {
            error_log("Erreur lors du changement de mot de passe : " . $e->getMessage());
            throw new Exception("Erreur lors du changement de mot de passe.");
        }
        return false;
    }

    public function isAdmin() {
        return $this->role === 'Administrateur';
    }
}
