<?php

class Rental {
    private $id;
    private $client_id;
    private $vehicule_id;
    private $offer_id;
    private $date_debut;
    private $date_fin;
    private $prix_total;
    private $status;

    public function __construct($id, $client_id, $vehicule_id, $offer_id, $date_debut, $date_fin, $prix_total, $status) {
        $this->id = $id;
        $this->client_id = $client_id;
        $this->vehicule_id = $vehicule_id;
        $this->offer_id = $offer_id;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->prix_total = $prix_total;
        $this->status = $status;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getClientId() { return $this->client_id; }
    public function getVehiculeId() { return $this->vehicule_id; }
    public function getOfferId() { return $this->offer_id; }
    public function getDateDebut() { return $this->date_debut; }
    public function getDateFin() { return $this->date_fin; }
    public function getPrixTotal() { return $this->prix_total; }
    public function getStatus() { return $this->status; }

    public static function create($data) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO rentals (client_id, vehicule_id, offer_id, date_debut, date_fin, prix_total, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['client_id'],
            $data['vehicule_id'],
            $data['offer_id'],
            $data['date_debut'],
            $data['date_fin'],
            $data['prix_total'],
            $data['status']
        ]);
        if ($stmt->rowCount() > 0) {
            return new Rental($conn->lastInsertId(), $data['client_id'], $data['vehicule_id'], $data['offer_id'], $data['date_debut'], $data['date_fin'], $data['prix_total'], $data['status']);
        }
        return null;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM rentals WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Rental($row['id'], $row['client_id'], $row['vehicule_id'], $row['offer_id'], $row['date_debut'], $row['date_fin'], $row['prix_total'], $row['status']);
        }
        return null;
    }

    public static function findByUserId($userId, $page = 1, $perPage = 10, $status = null) {
        global $conn;
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM rentals WHERE client_id = ?";
        $params = [$userId];
        
        if ($status !== null) {
            $query .= " AND status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY date_debut DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        $rentals = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rentals[] = new Rental($row['id'], $row['client_id'], $row['vehicule_id'], $row['offer_id'], $row['date_debut'], $row['date_fin'], $row['prix_total'], $row['status']);
        }
        return $rentals;
    }

    public static function countByUserId($userId, $status = null) {
        global $conn;
        $query = "SELECT COUNT(*) FROM rentals WHERE client_id = ?";
        $params = [$userId];
        
        if ($status !== null) {
            $query .= " AND status = ?";
            $params[] = $status;
        }
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE rentals SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $this->id]);
        return $stmt->rowCount() > 0;
    }
}
