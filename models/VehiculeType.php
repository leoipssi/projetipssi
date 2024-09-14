<?php
require_once __DIR__ . '/Database.php';

class VehiculeType {
    private $id;
    private $nom;

    public function __construct($id, $nom) {
        $this->id = $id;
        $this->nom = $nom;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    }

    // Setters
    public function setNom($nom) {
        $this->nom = $nom;
    }

    private static function getDB() {
        return Database::getInstance()->getConnection();
    }

    public static function create($nom) {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO vehicule_types (nom) VALUES (:nom)");
        $stmt->execute(['nom' => $nom]);
        if ($stmt->rowCount() > 0) {
            return new VehiculeType($db->lastInsertId(), $nom);
        }
        return null;
    }

    public static function getAll() {
        $db = self::getDB();
        $stmt = $db->query("SELECT * FROM vehicule_types");
        $types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $types[] = new VehiculeType($row['id'], $row['nom']);
        }
        return $types;
    }

    public static function findById($id) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM vehicule_types WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new VehiculeType($row['id'], $row['nom']);
        }
        return null;
    }

    public function update() {
        $db = self::getDB();
        $stmt = $db->prepare("UPDATE vehicule_types SET nom = :nom WHERE id = :id");
        return $stmt->execute([
            'nom' => $this->nom,
            'id' => $this->id
        ]);
    }

    public static function delete($id) {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM vehicule_types WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function count() {
        $db = self::getDB();
        $stmt = $db->query("SELECT COUNT(*) FROM vehicule_types");
        return $stmt->fetchColumn();
    }

    public static function findByName($nom) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM vehicule_types WHERE nom = :nom");
        $stmt->execute(['nom' => $nom]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new VehiculeType($row['id'], $row['nom']);
        }
        return null;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'nom' => $this->nom
        ];
    }

    public static function getPaginated($page = 1, $perPage = 10) {
        $db = self::getDB();
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("SELECT * FROM vehicule_types LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $types[] = new VehiculeType($row['id'], $row['nom']);
        }
        return $types;
    }

    public static function search($term) {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM vehicule_types WHERE nom LIKE :term");
        $stmt->execute(['term' => "%$term%"]);
        $types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $types[] = new VehiculeType($row['id'], $row['nom']);
        }
        return $types;
    }
}
