<?php
class TypeVehicule {
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
        $stmt = $conn->prepare("INSERT INTO type_vehicules (nom) VALUES (?)");
        $stmt->execute([$nom]);
        if ($stmt->rowCount() > 0) {
            return new TypeVehicule($conn->lastInsertId(), $nom);
        }
        return null;
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM type_vehicules");
        $types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $types[] = new TypeVehicule($row['id'], $row['nom']);
        }
        return $types;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM type_vehicules WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new TypeVehicule($row['id'], $row['nom']);
        }
        return null;
    }

    public function update($nom) {
        global $conn;
        $stmt = $conn->prepare("UPDATE type_vehicules SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $this->id]);
        return $stmt->rowCount() > 0;
    }

    public static function delete($id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM type_vehicules WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
