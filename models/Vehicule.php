<?php
require_once __DIR__ . '/../models/Database.php';

class Vehicule {
    private $id;
    private $type_id;
    private $marque;
    private $modele;
    private $numero_serie;
    private $couleur;
    private $immatriculation;
    private $kilometres;
    private $date_achat;
    private $prix_achat;
    private $categorie;
    private $tarif_journalier;
    private $is_available;

    private static $logLevel = 'ERROR';

    public static function setLogLevel($level) {
        self::$logLevel = $level;
    }

    private static function log($message, $level = 'INFO') {
        if (self::$logLevel == 'DEBUG' || (self::$logLevel == 'ERROR' && $level == 'ERROR')) {
            error_log("[Vehicule] [$level] $message");
        }
    }

    private static function getDB() {
        return Database::getInstance()->getConnection();
    }

    public static function isDbConnected() {
        return Database::getInstance()->getConnection() instanceof PDO;
    }

    public function __construct($id, $type_id, $marque, $modele, $numero_serie, $couleur, $immatriculation, $kilometres, $date_achat, $prix_achat, $categorie = null, $tarif_journalier = null, $is_available = true) {
        $this->id = (int)$id;
        $this->type_id = (int)$type_id;
        $this->marque = (string)$marque;
        $this->modele = (string)$modele;
        $this->numero_serie = (string)$numero_serie;
        $this->couleur = (string)$couleur;
        $this->immatriculation = (string)$immatriculation;
        $this->kilometres = (int)$kilometres;
        $this->date_achat = (string)$date_achat;
        $this->prix_achat = (float)$prix_achat;
        $this->categorie = $categorie ? (string)$categorie : null;
        $this->tarif_journalier = $tarif_journalier ? (float)$tarif_journalier : null;
        $this->is_available = (bool)$is_available;
    }

    public function getMarque() {
        return is_array($this->marque) ? json_encode($this->marque) : (string)$this->marque;
    }

    public function getId() {
        return (int)$this->id;
    }

    public function getModele() {
        return is_array($this->modele) ? json_encode($this->modele) : (string)$this->modele;
    }

    public function getTypeId() {
        return (int)$this->type_id;
    }

    public function setAvailable($available) {
        $this->is_available = (bool)$available;
    }

    public function getType() {
        try {
            $stmt = self::getDB()->prepare("SELECT nom FROM vehicule_types WHERE id = ?");
            $stmt->execute([$this->type_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (string)$result['nom'] : 'Type inconnu';
        } catch (PDOException $e) {
            self::log("Erreur lors de la récupération du type de véhicule : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de récupérer le type de véhicule.");
        }
    }

    public function calculerTarif($duree) {
        return (float)$this->tarif_journalier * (int)$duree;
    }

    public static function create($data) {
        $defaultData = [
            'type_id' => null,
            'marque' => '',
            'modele' => '',
            'numero_serie' => '',
            'couleur' => '',
            'immatriculation' => '',
            'kilometres' => 0,
            'date_achat' => date('Y-m-d'),
            'prix_achat' => 0,
            'categorie' => null,
            'tarif_journalier' => null,
            'is_available' => true
        ];
        
        $data = array_merge($defaultData, $data);

        try {
            $stmt = self::getDB()->prepare("INSERT INTO vehicules (type_id, marque, modele, numero_serie, couleur, immatriculation, kilometres, date_achat, prix_achat, categorie, tarif_journalier, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['type_id'],
                $data['marque'],
                $data['modele'],
                $data['numero_serie'],
                $data['couleur'],
                $data['immatriculation'],
                $data['kilometres'],
                $data['date_achat'],
                $data['prix_achat'],
                $data['categorie'],
                $data['tarif_journalier'],
                $data['is_available']
            ]);
            if ($stmt->rowCount() > 0) {
                return new self(
                    self::getDB()->lastInsertId(),
                    $data['type_id'],
                    $data['marque'],
                    $data['modele'],
                    $data['numero_serie'],
                    $data['couleur'],
                    $data['immatriculation'],
                    $data['kilometres'],
                    $data['date_achat'],
                    $data['prix_achat'],
                    $data['categorie'],
                    $data['tarif_journalier'],
                    $data['is_available']
                );
            }
        } catch (PDOException $e) {
            self::log("Erreur lors de la création du véhicule : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de créer le véhicule.");
        }
        return null;
    }

    public function update($data) {
        try {
            $stmt = self::getDB()->prepare("UPDATE vehicules SET type_id = ?, marque = ?, modele = ?, numero_serie = ?, couleur = ?, immatriculation = ?, kilometres = ?, date_achat = ?, prix_achat = ?, categorie = ?, tarif_journalier = ?, is_available = ? WHERE id = ?");
            $stmt->execute([
                $data['type_id'] ?? $this->type_id,
                $data['marque'] ?? $this->marque,
                $data['modele'] ?? $this->modele,
                $data['numero_serie'] ?? $this->numero_serie,
                $data['couleur'] ?? $this->couleur,
                $data['immatriculation'] ?? $this->immatriculation,
                $data['kilometres'] ?? $this->kilometres,
                $data['date_achat'] ?? $this->date_achat,
                $data['prix_achat'] ?? $this->prix_achat,
                $data['categorie'] ?? $this->categorie,
                $data['tarif_journalier'] ?? $this->tarif_journalier,
                $data['is_available'] ?? $this->is_available,
                $this->id
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            self::log("Erreur lors de la mise à jour du véhicule : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de mettre à jour le véhicule.");
        }
    }

    public static function findAll($page = 1, $perPage = 10) {
        self::log("Tentative de récupération des véhicules. Page: $page, PerPage: $perPage", 'DEBUG');
        $offset = ($page - 1) * $perPage;
        try {
            $stmt = self::getDB()->prepare("SELECT * FROM vehicules LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $vehicules = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vehicules[] = new self(
                    (int)$row['id'],
                    (int)$row['type_id'],
                    (string)$row['marque'],
                    (string)$row['modele'],
                    (string)$row['numero_serie'],
                    (string)$row['couleur'],
                    (string)$row['immatriculation'],
                    (int)$row['kilometres'],
                    (string)$row['date_achat'],
                    (float)$row['prix_achat'],
                    $row['categorie'] ? (string)$row['categorie'] : null,
                    $row['tarif_journalier'] ? (float)$row['tarif_journalier'] : null,
                    (bool)$row['is_available']
                );
            }
            return $vehicules;
        } catch (PDOException $e) {
            self::log("Erreur lors de la récupération de tous les véhicules : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de récupérer la liste des véhicules.");
        }
    }

    public static function findById($id) {
        self::log("Tentative de récupération du véhicule avec l'ID : $id", 'DEBUG');
        try {
            $stmt = self::getDB()->prepare("SELECT * FROM vehicules WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                foreach ($row as $key => $value) {
                    if (is_array($value)) {
                        self::log("Champ inattendu de type array dans vehicules pour l'ID $id : $key", 'WARNING');
                        $row[$key] = is_array($value) ? json_encode($value) : (string)$value;
                    }
                }
                
                return new self(
                    (int)$row['id'],
                    (int)$row['type_id'],
                    (string)$row['marque'],
                    (string)$row['modele'],
                    (string)$row['numero_serie'],
                    (string)$row['couleur'],
                    (string)$row['immatriculation'],
                    (int)$row['kilometres'],
                    (string)$row['date_achat'],
                    (float)$row['prix_achat'],
                    $row['categorie'] ? (string)$row['categorie'] : null,
                    $row['tarif_journalier'] ? (float)$row['tarif_journalier'] : null,
                    (bool)$row['is_available']
                );
            } else {
                self::log("Aucun véhicule trouvé avec l'ID : $id", 'WARNING');
                return null;
            }
        } catch (PDOException $e) {
            self::log("Erreur lors de la recherche du véhicule par ID : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de trouver le véhicule spécifié.");
        }
    }

    public static function findAvailable() {
        try {
            $stmt = self::getDB()->query("SELECT * FROM vehicules WHERE is_available = TRUE");
            $vehicules = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vehicules[] = new self(
                    (int)$row['id'],
                    (int)$row['type_id'],
                    (string)$row['marque'],
                    (string)$row['modele'],
                    (string)$row['numero_serie'],
                    (string)$row['couleur'],
                    (string)$row['immatriculation'],
                    (int)$row['kilometres'],
                    (string)$row['date_achat'],
                    (float)$row['prix_achat'],
                    $row['categorie'] ? (string)$row['categorie'] : null,
                    $row['tarif_journalier'] ? (float)$row['tarif_journalier'] : null,
                    (bool)$row['is_available']
                );
            }
            return $vehicules;
        } catch (PDOException $e) {
            self::log("Erreur lors de la recherche des véhicules disponibles : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de récupérer la liste des véhicules disponibles.");
        }
    }

    public static function findRented() {
        try {
            $stmt = self::getDB()->query("SELECT v.* FROM vehicules v JOIN rentals r ON v.id = r.vehicule_id WHERE r.status = 'En cours'");
            $vehicules = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vehicules[] = new self(
                    (int)$row['id'],
                    (int)$row['type_id'],
                    (string)$row['marque'],
                    (string)$row['modele'],
                    (string)$row['numero_serie'],
                    (string)$row['couleur'],
                    (string)$row['immatriculation'],
                    (int)$row['kilometres'],
                    (string)$row['date_achat'],
                    (float)$row['prix_achat'],
                    $row['categorie'] ? (string)$row['categorie'] : null,
                    $row['tarif_journalier'] ? (float)$row['tarif_journalier'] : null,
                    false
                );
            }
            return $vehicules;
        } catch (PDOException $e) {
            self::log("Erreur lors de la recherche des véhicules loués : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de récupérer la liste des véhicules loués.");
        }
    }

    public static function count() {
        try {
            $stmt = self::getDB()->query("SELECT COUNT(*) FROM vehicules");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            self::log("Erreur lors du comptage des véhicules : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de compter le nombre de véhicules.");
        }
    }

    public static function getRecentVehicules($limit = 5) {
        try {
            $stmt = self::getDB()->prepare("SELECT * FROM vehicules ORDER BY date_achat DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $vehicules = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                self::log("Création d'un objet Vehicule avec ID: " . $row['id'], 'DEBUG');
                $vehicule = new self(
                    (int)$row['id'],
                    (int)$row['type_id'],
                    (string)$row['marque'],
                    (string)$row['modele'],
                    (string)$row['numero_serie'],
                    (string)$row['couleur'],
                    (string)$row['immatriculation'],
                    (int)$row['kilometres'],
                    (string)$row['date_achat'],
                    (float)$row['prix_achat'],
                    $row['categorie'] ? (string)$row['categorie'] : null,
                    $row['tarif_journalier'] ? (float)$row['tarif_journalier'] : null,
                    (bool)$row['is_available']
                );
                $vehicules[] = $vehicule;
            }
            self::log("Nombre de véhicules récents récupérés : " . count($vehicules), 'DEBUG');
            return $vehicules;
        } catch (PDOException $e) {
            self::log("Erreur lors de la récupération des véhicules récents : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de récupérer les véhicules récents.");
        }
    }

    public static function getTopRented($limit = 5) {
        self::log("Tentative de récupération des $limit véhicules les plus loués", 'DEBUG');
        try {
            $stmt = self::getDB()->prepare("
                SELECT v.*, COUNT(r.id) as rental_count, SUM(r.prix_total) as revenue
                FROM vehicules v
                LEFT JOIN rentals r ON v.id = r.vehicule_id
                GROUP BY v.id
                ORDER BY rental_count DESC
                LIMIT :limit
            ");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $vehicules = [];
            foreach ($results as $row) {
                foreach ($row as $key => $value) {
                    if (is_array($value)) {
                        self::log("Champ inattendu de type array dans getTopRented : $key", 'WARNING');
                        $row[$key] = is_array($value) ? json_encode($value) : (string)$value;
                    } else {
                        // Assurez-vous que toutes les valeurs sont des chaînes
                        $row[$key] = (string)$value;
                    }
                }
                $vehicules[] = new self(
                    (int)$row['id'],
                    (int)$row['type_id'],
                    (string)$row['marque'],
                    (string)$row['modele'],
                    (string)$row['numero_serie'],
                    (string)$row['couleur'],
                    (string)$row['immatriculation'],
                    (int)$row['kilometres'],
                    (string)$row['date_achat'],
                    (float)$row['prix_achat'],
                    $row['categorie'] ? (string)$row['categorie'] : null,
                    $row['tarif_journalier'] ? (float)$row['tarif_journalier'] : null,
                    (bool)$row['is_available']
                );
            }
            
            return $vehicules;
        } catch (PDOException $e) {
            self::log("Erreur lors de la récupération des véhicules les plus loués : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de récupérer les véhicules les plus loués.");
        }
    }

    private static function checkDbConnection() {
        if (!self::isDbConnected()) {
            self::log("La connexion à la base de données n'est pas établie dans la classe Vehicule", 'ERROR');
            throw new Exception("La connexion à la base de données n'est pas établie.");
        }
    }

    public function delete() {
        try {
            $stmt = self::getDB()->prepare("DELETE FROM vehicules WHERE id = ?");
            $stmt->execute([$this->id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            self::log("Erreur lors de la suppression du véhicule : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de supprimer le véhicule.");
        }
    }

    public static function search($criteria) {
        $sql = "SELECT * FROM vehicules WHERE 1=1";
        $params = [];

        if (!empty($criteria['marque'])) {
            $sql .= " AND marque LIKE ?";
            $params[] = '%' . $criteria['marque'] . '%';
        }
        if (!empty($criteria['modele'])) {
            $sql .= " AND modele LIKE ?";
            $params[] = '%' . $criteria['modele'] . '%';
        }
        if (isset($criteria['is_available'])) {
            $sql .= " AND is_available = ?";
            $params[] = $criteria['is_available'];
        }

        try {
            $stmt = self::getDB()->prepare($sql);
            $stmt->execute($params);
            $vehicules = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vehicules[] = new self(
                    (int)$row['id'],
                    (int)$row['type_id'],
                    (string)$row['marque'],
                    (string)$row['modele'],
                    (string)$row['numero_serie'],
                    (string)$row['couleur'],
                    (string)$row['immatriculation'],
                    (int)$row['kilometres'],
                    (string)$row['date_achat'],
                    (float)$row['prix_achat'],
                    $row['categorie'] ? (string)$row['categorie'] : null,
                    $row['tarif_journalier'] ? (float)$row['tarif_journalier'] : null,
                    (bool)$row['is_available']
                );
            }
            return $vehicules;
        } catch (PDOException $e) {
            self::log("Erreur lors de la recherche de véhicules : " . $e->getMessage(), 'ERROR');
            throw new Exception("Impossible de rechercher les véhicules.");
        }
    }

    // Ajoutez ici d'autres méthodes getter si nécessaire
    public function getCouleur() {
        return is_array($this->couleur) ? json_encode($this->couleur) : (string)$this->couleur;
    }

    public function getImmatriculation() {
        return is_array($this->immatriculation) ? json_encode($this->immatriculation) : (string)$this->immatriculation;
    }

    public function getKilometres() {
        return (int)$this->kilometres;
    }

    public function getDateAchat() {
        return (string)$this->date_achat;
    }

    public function getPrixAchat() {
        return (float)$this->prix_achat;
    }

    public function getCategorie() {
        return $this->categorie ? (string)$this->categorie : null;
    }

    public function getTarifJournalier() {
        return $this->tarif_journalier ? (float)$this->tarif_journalier : null;
    }

    public function isAvailable() {
        return (bool)$this->is_available;
    }
}
