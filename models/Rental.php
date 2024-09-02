<?php
class Rental {
    private $id;
    private $client_id;
    private $vehicle_id;
    private $offer_id;
    private $date_debut;
    private $date_fin;
    private $status;

    public function __construct($id, $client_id, $vehicle_id, $offer_id, $date_debut, $date_fin, $status) {
        $this->id = $id;
        $this->client_id = $client_id;
        $this->vehicle_id = $vehicle_id;
        $this->offer_id = $offer_id;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->status = $status;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getClientId() { return $this->client_id; }
    public function getVehicleId() { return $this->vehicle_id; }
    public function getOfferId() { return $this->offer_id; }
    public function getDateDebut() { return $this->date_debut; }
    public function getDateFin() { return $this->date_fin; }
    public function getStatus() { return $this->status; }

    public static function create($client_id, $vehicle_id, $offer_id, $date_debut, $date_fin) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO rentals (client_id, vehicle_id, offer_id, date_debut, date_fin, status) VALUES (?, ?, ?, ?, ?, 'En cours')");
        $stmt->execute([$client_id, $vehicle_id, $offer_id, $date_debut, $date_fin]);
        
        if ($stmt->rowCount() > 0) {
            return new Rental($conn->lastInsertId(), $client_id, $vehicle_id, $offer_id, $date_debut, $date_fin, 'En cours');
        }
        return null;
    }

    public static function findByUserId($userId) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM rentals WHERE client_id = ?");
        $stmt->execute([$userId]);
        $rentals = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rentals[] = new Rental($row['id'], $row['client_id'], $row['vehicle_id'], $row['offer_id'], $row['date_debut'], $row['date_fin'], $row['status']);
        }
        return $rentals;
    }

    public static function count() {
        global $conn;
        $stmt = $conn->query("SELECT COUNT(*) FROM rentals");
        return $stmt->fetchColumn();
    }
}
