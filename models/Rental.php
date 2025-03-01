<?php
require_once __DIR__ . '/../models/Database.php';

class Rental {
    private $id;
    private $client_id;
    private $vehicule_id;
    private $offer_id;
    private $date_debut;
    private $date_fin;
    private $prix_total;
    private $status;
    private $client_name;
    private $vehicule_name;

    private static function getDB() {
        return Database::getInstance()->getConnection();
    }

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
        try {
            $conn = self::getDB();
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
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::create : " . $e->getMessage());
            throw new Exception("Impossible de créer la location.");
        }
    }

    public static function findById($id) {
        try {
            $conn = self::getDB();
            $stmt = $conn->prepare("SELECT * FROM rentals WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Rental($row['id'], $row['client_id'], $row['vehicule_id'], $row['offer_id'], $row['date_debut'], $row['date_fin'], $row['prix_total'], $row['status']);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::findById : " . $e->getMessage());
            throw new Exception("Impossible de trouver la location.");
        }
    }

    public static function findByUserId($userId, $page = 1, $perPage = 10, $status = null) {
        try {
            $conn = self::getDB();
            $offset = ($page - 1) * $perPage;
            $query = "SELECT * FROM rentals WHERE client_id = :userId";
            $params = [':userId' => $userId];
            
            if ($status !== null) {
                $query .= " AND status = :status";
                $params[':status'] = $status;
            }
            
            $query .= " ORDER BY date_debut DESC LIMIT :limit OFFSET :offset";
            $params[':limit'] = $perPage;
            $params[':offset'] = $offset;
            
            $stmt = $conn->prepare($query);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val, PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $rentals = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rentals[] = new Rental($row['id'], $row['client_id'], $row['vehicule_id'], $row['offer_id'], $row['date_debut'], $row['date_fin'], $row['prix_total'], $row['status']);
            }
            return $rentals;
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::findByUserId : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les locations de l'utilisateur.");
        }
    }

    public static function countByUserId($userId, $status = null) {
        try {
            $conn = self::getDB();
            $query = "SELECT COUNT(*) FROM rentals WHERE client_id = :userId";
            $params = [':userId' => $userId];
            
            if ($status !== null) {
                $query .= " AND status = :status";
                $params[':status'] = $status;
            }
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::countByUserId : " . $e->getMessage());
            throw new Exception("Impossible de compter les locations de l'utilisateur.");
        }
    }

    public function update($data) {
        try {
            $conn = self::getDB();
            $stmt = $conn->prepare("UPDATE rentals SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $this->id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::update : " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour la location.");
        }
    }

    public static function count() {
        try {
            $conn = self::getDB();
            $stmt = $conn->query("SELECT COUNT(*) FROM rentals");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::count : " . $e->getMessage());
            throw new Exception("Impossible de compter les locations.");
        }
    }

    public static function totalRevenue() {
        try {
            $conn = self::getDB();
            $stmt = $conn->query("SELECT SUM(prix_total) FROM rentals WHERE status = 'terminée'");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::totalRevenue : " . $e->getMessage());
            throw new Exception("Impossible de calculer le revenu total.");
        }
    }

    public static function getRecent($limit = 5) {
        try {
            $conn = self::getDB();
            $stmt = $conn->prepare("SELECT * FROM rentals ORDER BY date_debut DESC LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rentals = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rentals[] = new Rental($row['id'], $row['client_id'], $row['vehicule_id'], $row['offer_id'], $row['date_debut'], $row['date_fin'], $row['prix_total'], $row['status']);
            }
            return $rentals;
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::getRecent : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les locations récentes.");
        }
    }

    public static function getFiltered($page, $search, $status, $sortBy, $sortOrder) {
        try {
            $conn = self::getDB();
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            $sql = "SELECT r.*, u.username as client_name, CONCAT(v.marque, ' ', v.modele) as vehicule_name 
                    FROM rentals r
                    LEFT JOIN users u ON r.client_id = u.id
                    LEFT JOIN vehicules v ON r.vehicule_id = v.id
                    WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (u.username LIKE :search OR CONCAT(v.marque, ' ', v.modele) LIKE :search)";
                $params[':search'] = "%$search%";
            }

            if (!empty($status)) {
                $sql .= " AND r.status = :status";
                $params[':status'] = $status;
            }

            $allowedSortFields = ['id', 'client_name', 'vehicule_name', 'date_debut', 'date_fin', 'prix_total', 'status'];
            $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'id';
            $sortOrder = $sortOrder === 'DESC' ? 'DESC' : 'ASC';

            $sql .= " ORDER BY $sortBy $sortOrder LIMIT :limit OFFSET :offset";
            $params[':limit'] = $perPage;
            $params[':offset'] = $offset;

            $stmt = $conn->prepare($sql);
            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $rentals = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rental = new Rental($row['id'], $row['client_id'], $row['vehicule_id'], $row['offer_id'], $row['date_debut'], $row['date_fin'], $row['prix_total'], $row['status']);
                $rental->client_name = $row['client_name'];
                $rental->vehicule_name = $row['vehicule_name'];
                $rentals[] = $rental;
            }
            return $rentals;
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::getFiltered : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les locations filtrées.");
        }
    }
    
    public static function getTotalPages($search, $status) {
        try {
            $conn = self::getDB();
            $perPage = 10;

            $sql = "SELECT COUNT(*) 
                    FROM rentals r
                    LEFT JOIN users u ON r.client_id = u.id
                    LEFT JOIN vehicules v ON r.vehicule_id = v.id
                    WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (u.username LIKE :search OR CONCAT(v.marque, ' ', v.modele) LIKE :search)";
                $params[':search'] = "%$search%";
            }

            if (!empty($status)) {
                $sql .= " AND r.status = :status";
                $params[':status'] = $status;
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $total = $stmt->fetchColumn();

            return ceil($total / $perPage);
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::getTotalPages : " . $e->getMessage());
            throw new Exception("Impossible de calculer le nombre total de pages.");
        }
    }

    public function getClientName() {
        return $this->client_name ?? '';
    }

    public function getVehiculeName() {
        return $this->vehicule_name ?? '';
    }

    public static function getRentedVehicules() {
        try {
            $conn = self::getDB();
            $stmt = $conn->prepare("SELECT DISTINCT vehicule_id FROM rentals WHERE status = 'active'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Erreur dans Rental::getRentedVehicules : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les véhicules loués.");
        }
    }

    public function cancel() {
        if ($this->status === 'active') {
            $this->status = 'cancelled';
            return $this->update(['status' => 'cancelled']);
        }
        return false;
    }
}
