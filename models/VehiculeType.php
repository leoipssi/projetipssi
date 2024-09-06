<?php
class VehiculeType {
    private $id;
    private $nom;

    public function __construct($id, $nom) {
        $this->id = $id;
        $this->nom = $nom;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }

    // Setters
    public function setNom($nom) { $this->nom = $nom; }

    public static function create($nom) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO vehicule_types (nom) VALUES (?)");
        $stmt->execute([$nom]);
        if ($stmt->rowCount() > 0) {
            return new VehiculeType($conn->lastInsertId(), $nom);
        }
        return null;
    }

    public static function getAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM vehicule_types");
        $types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $types[] = new VehiculeType($row['id'], $row['nom']);
        }
        return $types;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicule_types WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new VehiculeType($row['id'], $row['nom']);
        }
        return null;
    }

    public function update() {
        global $conn;
        $stmt = $conn->prepare("UPDATE vehicule_types SET nom = ? WHERE id = ?");
        $stmt->execute([$this->nom, $this->id]);
        return $stmt->rowCount() > 0;
    }

    public static function delete($id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM vehicule_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
