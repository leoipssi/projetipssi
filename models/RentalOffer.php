<?php
class RentalOffer {
    private $id;
    private $vehicule_id;
    private $duree;
    private $kilometres;
    private $prix;
    private $is_active;

    public function __construct($id, $vehicule_id, $duree, $kilometres, $prix, $is_active) {
        $this->id = $id;
        $this->vehicule_id = $vehicule_id;
        $this->duree = $duree;
        $this->kilometres = $kilometres;
        $this->prix = $prix;
        $this->is_active = $is_active;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getVehiculeId() { return $this->vehicule_id; }
    public function getDuree() { return $this->duree; }
    public function getKilometres() { return $this->kilometres; }
    public function getPrix() { return $this->prix; }
    public function isActive() { return $this->is_active; }

    public static function create($data) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO location_offers (vehicule_id, duree, kilometres, prix, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['vehicule_id'],
            $data['duree'],
            $data['kilometres'],
            $data['prix'],
            $data['is_active'] ?? true
        ]);
        if ($stmt->rowCount() > 0) {
            return new RentalOffer($conn->lastInsertId(), $data['vehicule_id'], $data['duree'], $data['kilometres'], $data['prix'], $data['is_active'] ?? true);
        }
        return null;
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM location_offers");
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer($row['id'], $row['vehicule_id'], $row['duree'], $row['kilometres'], $row['prix'], $row['is_active']);
        }
        return $offers;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM location_offers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new RentalOffer($row['id'], $row['vehicule_id'], $row['duree'], $row['kilometres'], $row['prix'], $row['is_active']);
        }
        return null;
    }

    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE location_offers SET vehicule_id = ?, duree = ?, kilometres = ?, prix = ?, is_active = ? WHERE id = ?");
        $stmt->execute([
            $data['vehicule_id'] ?? $this->vehicule_id,
            $data['duree'] ?? $this->duree,
            $data['kilometres'] ?? $this->kilometres,
            $data['prix'] ?? $this->prix,
            $data['is_active'] ?? $this->is_active,
            $this->id
        ]);
        return $stmt->rowCount() > 0;
    }
    public static function getActiveOffers() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM location_offers WHERE is_active = TRUE");
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer($row['id'], $row['vehicule_type_id'], $row['duree'], $row['kilometres'], $row['prix'], $row['is_active']);
        }
        return $offers;
    }

    public function delete() {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM location_offers WHERE id = ?");
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }

    public function hasActiveRentals() {
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rentals WHERE offer_id = ? AND status = 'active'");
        $stmt->execute([$this->id]);
        return $stmt->fetchColumn() > 0;
    }
}
