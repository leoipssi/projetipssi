<?php
class RentalOffer {
    private $id;
    private $vehicle_id;
    private $duree;
    private $kilometres;
    private $prix;
    private $is_active;

    public function __construct($id, $vehicle_id, $duree, $kilometres, $prix, $is_active) {
        $this->id = $id;
        $this->vehicle_id = $vehicle_id;
        $this->duree = $duree;
        $this->kilometres = $kilometres;
        $this->prix = $prix;
        $this->is_active = $is_active;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getVehicleId() { return $this->vehicle_id; }
    public function getDuree() { return $this->duree; }
    public function getKilometres() { return $this->kilometres; }
    public function getPrix() { return $this->prix; }
    public function isActive() { return $this->is_active; }

    public static function getActiveOffers($limit = null) {
        global $conn;
        $sql = "SELECT * FROM rental_offers WHERE is_active = 1";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $conn->query($sql);
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer($row['id'], $row['vehicle_id'], $row['duree'], $row['kilometres'], $row['prix'], $row['is_active']);
        }
        return $offers;
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM rental_offers");
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer($row['id'], $row['vehicle_id'], $row['duree'], $row['kilometres'], $row['prix'], $row['is_active']);
        }
        return $offers;
    }

    public function toggleActive() {
        global $conn;
        $this->is_active = !$this->is_active;
        $stmt = $conn->prepare("UPDATE rental_offers SET is_active = ? WHERE id = ?");
        $stmt->execute([$this->is_active, $this->id]);
        return $stmt->rowCount() > 0;
    }
}
