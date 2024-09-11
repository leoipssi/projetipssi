<?php
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

    private static $db;

    public static function setDB($db) {
        self::$db = $db;
        error_log("Database connection set for Vehicule class");
    }

    public function __construct($id, $type_id, $marque, $modele, $numero_serie, $couleur, $immatriculation, $kilometres, $date_achat, $prix_achat, $categorie = null, $tarif_journalier = null, $is_available = true) {
        $this->id = $id;
        $this->type_id = $type_id;
        $this->marque = $marque;
        $this->modele = $modele;
        $this->numero_serie = $numero_serie;
        $this->couleur = $couleur;
        $this->immatriculation = $immatriculation;
        $this->kilometres = $kilometres;
        $this->date_achat = $date_achat;
        $this->prix_achat = $prix_achat;
        $this->categorie = $categorie;
        $this->tarif_journalier = $tarif_journalier;
        $this->is_available = $is_available;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTypeId() { return $this->type_id; }
    public function getMarque() { return $this->marque; }
    public function getModele() { return $this->modele; }
    public function getNumeroSerie() { return $this->numero_serie; }
    public function getCouleur() { return $this->couleur; }
    public function getImmatriculation() { return $this->immatriculation; }
    public function getKilometres() { return $this->kilometres; }
    public function getDateAchat() { return $this->date_achat; }
    public function getPrixAchat() { return $this->prix_achat; }
    public function getCategorie() { return $this->categorie; }
    public function getTarifJournalier() { return $this->tarif_journalier; }
    public function isAvailable() { return $this->is_available; }

    public function setAvailable($available) {
        $this->is_available = $available;
    }

    public function getType() {
        self::checkDbConnection();
        $stmt = self::$db->prepare("SELECT nom FROM vehicule_types WHERE id = ?");
        $stmt->execute([$this->type_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nom'] : 'Type inconnu';
    }

    public function calculerTarif($duree) {
        return $this->tarif_journalier * $duree;
    }

    public static function create($data) {
        self::checkDbConnection();
        
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

        $stmt = self::$db->prepare("INSERT INTO vehicules (type_id, marque, modele, numero_serie, couleur, immatriculation, kilometres, date_achat, prix_achat, categorie, tarif_journalier, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            return new Vehicule(
                self::$db->lastInsertId(),
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
        return null;
    }

    public function update($data) {
        self::checkDbConnection();
        $stmt = self::$db->prepare("UPDATE vehicules SET type_id = ?, marque = ?, modele = ?, numero_serie = ?, couleur = ?, immatriculation = ?, kilometres = ?, date_achat = ?, prix_achat = ?, categorie = ?, tarif_journalier = ?, is_available = ? WHERE id = ?");
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
    }

    public static function findAll() {
        self::checkDbConnection();
        error_log("Attempting to find all vehicles");
        $stmt = self::$db->query("SELECT * FROM vehicules");
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule(
                $row['id'],
                $row['type_id'],
                $row['marque'],
                $row['modele'],
                $row['numero_serie'],
                $row['couleur'],
                $row['immatriculation'],
                $row['kilometres'],
                $row['date_achat'],
                $row['prix_achat'],
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null,
                $row['is_available']
            );
        }
        error_log("Found " . count($vehicules) . " vehicles");
        return $vehicules;
    }

    public static function findById($id) {
        self::checkDbConnection();
        $stmt = self::$db->prepare("SELECT * FROM vehicules WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Vehicule(
                $row['id'],
                $row['type_id'],
                $row['marque'],
                $row['modele'],
                $row['numero_serie'],
                $row['couleur'],
                $row['immatriculation'],
                $row['kilometres'],
                $row['date_achat'],
                $row['prix_achat'],
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null,
                $row['is_available']
            );
        }
        return null;
    }

    public static function findAvailable() {
        self::checkDbConnection();
        $stmt = self::$db->query("SELECT * FROM vehicules WHERE is_available = TRUE");
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule(
                $row['id'],
                $row['type_id'],
                $row['marque'],
                $row['modele'],
                $row['numero_serie'],
                $row['couleur'],
                $row['immatriculation'],
                $row['kilometres'],
                $row['date_achat'],
                $row['prix_achat'],
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null,
                $row['is_available']
            );
        }
        return $vehicules;
    }

    public static function findRented() {
        self::checkDbConnection();
        $stmt = self::$db->query("SELECT v.* FROM vehicules v JOIN rentals r ON v.id = r.vehicule_id WHERE r.status = 'En cours'");
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule(
                $row['id'],
                $row['type_id'],
                $row['marque'],
                $row['modele'],
                $row['numero_serie'],
                $row['couleur'],
                $row['immatriculation'],
                $row['kilometres'],
                $row['date_achat'],
                $row['prix_achat'],
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null,
                false
            );
        }
        return $vehicules;
    }

    public static function count() {
        self::checkDbConnection();
        $stmt = self::$db->query("SELECT COUNT(*) FROM vehicules");
        return $stmt->fetchColumn();
    }

    public static function getRecentVehicules($limit = 5) {
        self::checkDbConnection();
        $stmt = self::$db->prepare("SELECT * FROM vehicules ORDER BY date_achat DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule(
                $row['id'],
                $row['type_id'],
                $row['marque'],
                $row['modele'],
                $row['numero_serie'],
                $row['couleur'],
                $row['immatriculation'],
                $row['kilometres'],
                $row['date_achat'],
                $row['prix_achat'],
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null,
                $row['is_available']
            );
        }
        return $vehicules;
    }

    public static function getTopRented($limit = 5) {
        self::checkDbConnection();
        $stmt = self::$db->prepare("
            SELECT v.*, COUNT(r.id) as rental_count, SUM(r.prix_total) as revenue
            FROM vehicules v
            LEFT JOIN rentals r ON v.id = r.vehicule_id
            GROUP BY v.id
            ORDER BY rental_count DESC
            LIMIT :limit
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private static function checkDbConnection() {
        if (!self::$db) {
            error_log("Database connection not established in Vehicule class");
            throw new Exception("La connexion à la base de données n'est pas établie.");
        }
    }
}
