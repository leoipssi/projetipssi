<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $role;
    private $created_at;

    public function __construct($id, $username, $email, $password, $role, $created_at) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getCreatedAt() { return $this->created_at; }

    public static function authenticate($username, $password) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return new User($user['id'], $user['username'], $user['email'], $user['password'], $user['role'], $user['created_at']);
        }
        return null;
    }

    public static function create($data) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->execute([$data['username'], $data['email'], $hashedPassword, 'Utilisateur']);
        
        if ($stmt->rowCount() > 0) {
            return new User($conn->lastInsertId(), $data['username'], $data['email'], $hashedPassword, 'Utilisateur', date('Y-m-d H:i:s'));
        }
        return null;
    }

    public static function findAllClients() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM users WHERE role = 'Utilisateur'");
        $clients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clients[] = new User($row['id'], $row['username'], $row['email'], $row['password'], $row['role'], $row['created_at']);
        }
        return $clients;
    }

    public static function count() {
        global $conn;
        $stmt = $conn->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }
}
