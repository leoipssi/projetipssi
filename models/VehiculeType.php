<?php
class VehicleType {
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
        $stmt = $conn->prepare("INSERT INTO vehicle_types (nom) VALUES (?)");
        $stmt->execute([$nom]);
        if ($stmt->rowCount() > 0) {
            return new VehicleType($conn->lastInsertId(), $nom);
        }
        return null;
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM vehicle_types");
        $types = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $types[] = new VehicleType($row['id'], $row['nom']);
        }
        return $types;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicle_types WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new VehicleType($row['id'], $row['nom']);
        }
        return null;
    }

    public function update($nom) {
        global $conn;
        $stmt = $conn->prepare("UPDATE vehicle_types SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $this->id]);
        return $stmt->rowCount() > 0;
    }

    public static function delete($id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM vehicle_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
