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
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashedPassword, $email, $role]);
        if ($stmt->rowCount() > 0) {
            return new User($conn->lastInsertId(), $username, $hashedPassword, $email, $role, date('Y-m-d H:i:s'));
        }
        return null;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row['id'], $row['username'], $row['password'], $row['email'], $row['role'], $row['created_at']);
        }
        return null;
    }

    public static function findByUsername($username) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row['id'], $row['username'], $row['password'], $row['email'], $row['role'], $row['created_at']);
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
        $stmt = $conn->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }
}
