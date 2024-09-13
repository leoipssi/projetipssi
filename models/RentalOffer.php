<?php
require_once __DIR__ . '/../models/Database.php';

class RentalOffer {
    private $id;
    private $vehicule_id;
    private $duree;
    private $kilometres;
    private $prix;
    private $is_active;
    private $is_available;

    private static function getDB() {
        return Database::getInstance()->getConnection();
    }

    public function __construct($id, $vehicule_id, $duree, $kilometres, $prix, $is_active, $is_available) {
        $this->id = $id;
        $this->vehicule_id = $vehicule_id;
        $this->duree = $duree;
        $this->kilometres = $kilometres;
        $this->prix = $prix;
        $this->is_active = $is_active;
        $this->is_available = $is_available;
    }

    // Getters restent inchangés

    public function setAvailable($available) {
        $this->is_available = $available;
    }
    
    public function getVehicule() {
        return Vehicule::findById($this->vehicule_id);
    }
    
    public static function create($data) {
        $db = self::getDB();
        try {
            $sql = "INSERT INTO location_offers (vehicule_id, duree, kilometres, prix, is_active, is_available) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['vehicule_id'],
                $data['duree'],
                $data['kilometres'],
                $data['prix'],
                $data['is_active'] ?? 1,
                $data['is_available'] ?? 1
            ]);
            
            if ($stmt->rowCount() > 0) {
                $id = $db->lastInsertId();
                return new self($id, $data['vehicule_id'], $data['duree'], $data['kilometres'], $data['prix'], 
                                $data['is_active'] ?? 1, $data['is_available'] ?? 1);
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'offre de location : " . $e->getMessage());
            throw new Exception("Impossible de créer l'offre de location.");
        }
        return null;
    }

    public static function getOffersForVehicle($vehiculeId) {
        $db = self::getDB();
        try {
            $sql = "SELECT * FROM location_offers WHERE vehicule_id = ? ORDER BY id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([$vehiculeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des offres pour le véhicule : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les offres pour ce véhicule.");
        }
    }

    public static function findAll() {
        $db = self::getDB();
        try {
            $stmt = $db->query("SELECT * FROM location_offers");
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de toutes les offres : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les offres de location.");
        }
    }

    public static function findById($id) {
        $db = self::getDB();
        try {
            $stmt = $db->prepare("SELECT * FROM location_offers WHERE id = ?");
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de l'offre par ID : " . $e->getMessage());
            throw new Exception("Impossible de trouver l'offre spécifiée.");
        }
        return null;
    }

    public function update($data) {
        $db = self::getDB();
        try {
            $stmt = $db->prepare("UPDATE location_offers SET vehicule_id = ?, duree = ?, kilometres = ?, prix = ?, is_active = ?, is_available = ? WHERE id = ?");
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'offre : " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour l'offre de location.");
        }
    }

    public static function getActiveOffers($limit = null) {
        $db = self::getDB();
        try {
            $sql = "SELECT * FROM location_offers WHERE is_active = TRUE AND is_available = TRUE";
            if ($limit !== null) {
                $sql .= " LIMIT " . intval($limit);
            }
            $stmt = $db->query($sql);
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des offres actives : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les offres actives.");
        }
    }

    public function delete() {
        $db = self::getDB();
        try {
            $stmt = $db->prepare("DELETE FROM location_offers WHERE id = ?");
            $stmt->execute([$this->id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'offre : " . $e->getMessage());
            throw new Exception("Impossible de supprimer l'offre de location.");
        }
    }

    public function hasActiveRentals() {
        $db = self::getDB();
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM rentals WHERE offer_id = ? AND status = 'En cours'");
            $stmt->execute([$this->id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification des locations actives : " . $e->getMessage());
            throw new Exception("Impossible de vérifier les locations actives pour cette offre.");
        }
    }

    public static function findAvailable() {
        $db = self::getDB();
        try {
            $stmt = $db->query("SELECT * FROM location_offers WHERE is_active = TRUE AND is_available = TRUE");
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des offres disponibles : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les offres disponibles.");
        }
    }

    public static function findActiveByVehiculeId($vehiculeId) {
        $db = self::getDB();
        try {
            $stmt = $db->prepare("SELECT * FROM location_offers WHERE vehicule_id = ? AND is_active = TRUE AND is_available = TRUE");
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des offres actives pour le véhicule : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les offres actives pour ce véhicule.");
        }
    }

    public function toggleActive() {
        $this->is_active = !$this->is_active;
        return $this->update(['is_active' => $this->is_active]);
    }

    public function hide() {
        return $this->update(['is_active' => false]);
    }
}
