<?php
class User {
    private $id;
    private $username;
    private $password;
    private $email;
    private $role;
    private $created_at;

    public function __construct($id, $username, $password, $email, $role, $created_at) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getCreatedAt() { return $this->created_at; }

    public static function create($username, $password, $email, $role = 'Utilisateur') {
        global $conn;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $email, $role]);
            if ($stmt->rowCount() > 0) {
                return new User($conn->lastInsertId(), $username, $hashedPassword, $email, $role, date('Y-m-d H:i:s'));
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
                return new User($row['id'], $row['username'], $row['password'], $row['email'], $row['role'], $row['created_at']);
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
                return new User($row['id'], $row['username'], $row['password'], $row['email'], $row['role'], $row['created_at']);
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
                return new User($row['id'], $row['username'], $row['password'], $row['email'], $row['role'], $row['created_at']);
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
        $allowedFields = ['username', 'email', 'role'];
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
}
