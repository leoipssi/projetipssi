<?php
class RentalOffer {
    private $id;
    private $vehicule_id;
    private $duree;
    private $kilometres;
    private $prix;
    private $is_active;
    private $is_available;

    public function __construct($id, $vehicule_id, $duree, $kilometres, $prix, $is_active, $is_available) {
        $this->id = $id;
        $this->vehicule_id = $vehicule_id;
        $this->duree = $duree;
        $this->kilometres = $kilometres;
        $this->prix = $prix;
        $this->is_active = $is_active;
        $this->is_available = $is_available;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getVehiculeId() { return $this->vehicule_id; }
    public function getDuree() { return $this->duree; }
    public function getKilometres() { return $this->kilometres; }
    public function getPrix() { return $this->prix; }
    public function isActive() { return $this->is_active; }
    public function isAvailable() { return $this->is_available; }

    public function setAvailable($available) {
        $this->is_available = $available;
    }
    
    public function getVehicule() {
        return Vehicule::findById($this->vehicule_id);
    }
    
    public static function create($data) {
        global $conn;
        $sql = "INSERT INTO location_offers (vehicule_id, duree, kilometres, prix, is_active, is_available) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data['vehicule_id'],
            $data['duree'],
            $data['kilometres'],
            $data['prix'],
            $data['is_active'] ?? 1,
            $data['is_available'] ?? 1
        ]);
        
        if ($stmt->rowCount() > 0) {
            $id = $conn->lastInsertId();
            return new self($id, $data['vehicule_id'], $data['duree'], $data['kilometres'], $data['prix'], 
                            $data['is_active'] ?? 1, $data['is_available'] ?? 1);
        }
        return null;
    }

    public static function getOffersForVehicle($vehiculeId) {
        global $conn;
        $sql = "SELECT * FROM location_offers WHERE vehicule_id = ? ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$vehiculeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM location_offers");
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer(
                $row['id'],
                $row['vehicule_id'],
                $row['duree'],
                $row['kilometres'],
                $row['prix'],
                $row['is_active'],
                $row['is_available']
            );
        }
        return $offers;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM location_offers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new RentalOffer(
                $row['id'],
                $row['vehicule_id'],
                $row['duree'],
                $row['kilometres'],
                $row['prix'],
                $row['is_active'],
                $row['is_available']
            );
        }
return null;
    }

    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE location_offers SET vehicule_id = ?, duree = ?, kilometres = ?, prix = ?, is_active = ?, is_available = ? WHERE id = ?");
        $stmt->execute([
            $data['vehicule_id'] ?? $this->vehicule_id,
            $data['duree'] ?? $this->duree,
            $data['kilometres'] ?? $this->kilometres,
            $data['prix'] ?? $this->prix,
            $data['is_active'] ?? $this->is_active,
            $data['is_available'] ?? $this->is_available,
            $this->id
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function getActiveOffers() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM location_offers WHERE is_active = TRUE AND is_available = TRUE");
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer(
                $row['id'],
                $row['vehicule_id'],
                $row['duree'],
                $row['kilometres'],
                $row['prix'],
                $row['is_active'],
                $row['is_available']
            );
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
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rentals WHERE offer_id = ? AND status = 'En cours'");
        $stmt->execute([$this->id]);
        return $stmt->fetchColumn() > 0;
    }

    public static function findAvailable() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM location_offers WHERE is_active = TRUE AND is_available = TRUE");
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer(
                $row['id'],
                $row['vehicule_id'],
                $row['duree'],
                $row['kilometres'],
                $row['prix'],
                $row['is_active'],
                $row['is_available']
            );
        }
        return $offers;
    }

    public static function findActiveByVehiculeId($vehiculeId) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM location_offers WHERE vehicule_id = ? AND is_active = TRUE AND is_available = TRUE");
        $stmt->execute([$vehiculeId]);
        $offers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offers[] = new RentalOffer(
                $row['id'],
                $row['vehicule_id'],
                $row['duree'],
                $row['kilometres'],
                $row['prix'],
                $row['is_active'],
                $row['is_available']
            );
        }
        return $offers;
    }

    public function toggleActive() {
        $this->is_active = !$this->is_active;
        return $this->update(['is_active' => $this->is_active]);
    }

    public function hide() {
        return $this->update(['is_active' => false]);
    }
}
