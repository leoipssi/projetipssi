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
    private $categorie; // 'scooter' ou 'voiture'
    private $tarif_journalier;

    public function __construct($id, $type_id, $marque, $modele, $numero_serie, $couleur, $immatriculation, $kilometres, $date_achat, $categorie, $tarif_journalier) {
        $this->id = $id;
        $this->type_id = $type_id;
        $this->marque = $marque;
        $this->modele = $modele;
        $this->numero_serie = $numero_serie;
        $this->couleur = $couleur;
        $this->immatriculation = $immatriculation;
        $this->kilometres = $kilometres;
        $this->date_achat = $date_achat;
        $this->categorie = $categorie;
        $this->tarif_journalier = $tarif_journalier;
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
    public function getCategorie() { return $this->categorie; }
    public function getTarifJournalier() { return $this->tarif_journalier; }

    public function getType() {
        global $conn;
        $stmt = $conn->prepare("SELECT nom FROM vehicle_types WHERE id = ?");
        $stmt->execute([$this->type_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nom'] : 'Type inconnu';
    }

    public function calculerTarif($duree) {
        return $this->tarif_journalier * $duree;
    }

    // Méthode pour créer un nouveau véhicule
    public static function create($data) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO vehicules (type_id, marque, modele, numero_serie, couleur, immatriculation, kilometres, date_achat, categorie, tarif_journalier) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['type_id'],
            $data['marque'],
            $data['modele'],
            $data['numero_serie'],
            $data['couleur'],
            $data['immatriculation'],
            $data['kilometres'],
            $data['date_achat'],
            $data['categorie'] ?? null,
            $data['tarif_journalier'] ?? null
        ]);
        if ($stmt->rowCount() > 0) {
            return new Vehicule(
                $conn->lastInsertId(),
                $data['type_id'],
                $data['marque'],
                $data['modele'],
                $data['numero_serie'],
                $data['couleur'],
                $data['immatriculation'],
                $data['kilometres'],
                $data['date_achat'],
                $data['categorie'] ?? null,
                $data['tarif_journalier'] ?? null
            );
        }
        return null;
    }

    // Méthode pour mettre à jour un véhicule existant
    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE vehicules SET type_id = ?, marque = ?, modele = ?, numero_serie = ?, couleur = ?, immatriculation = ?, kilometres = ?, date_achat = ?, categorie = ?, tarif_journalier = ? WHERE id = ?");
        $stmt->execute([
            $data['type_id'],
            $data['marque'],
            $data['modele'],
            $data['numero_serie'],
            $data['couleur'],
            $data['immatriculation'],
            $data['kilometres'],
            $data['date_achat'],
            $data['categorie'] ?? $this->categorie,
            $data['tarif_journalier'] ?? $this->tarif_journalier,
            $this->id
        ]);
        return $stmt->rowCount() > 0;
    }

    // Méthode pour récupérer tous les véhicules
    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM vehicules");
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
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null
            );
        }
        return $vehicules;
    }

    // Méthode statique pour obtenir tous les véhicules
    public static function getAll() {
        return self::findAll();
    }

    // Méthode pour trouver un véhicule par son ID
    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicules WHERE id = ?");
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
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null
            );
        }
        return null;
    }

    // Méthode pour compter le nombre total de véhicules
    public static function count() {
        global $conn;
        $stmt = $conn->query("SELECT COUNT(*) FROM vehicules");
        return $stmt->fetchColumn();
    }

    // Méthode pour obtenir les véhicules les plus récents
    public static function getRecentVehicules($limit = 5) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicules ORDER BY date_achat DESC LIMIT :limit");
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
                $row['categorie'] ?? null,
                $row['tarif_journalier'] ?? null
            );
        }
        return $vehicules;
    }

    // Méthode pour obtenir les véhicules les plus loués
    public static function getTopRented($limit = 5) {
        global $conn;
        $stmt = $conn->prepare("
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
}
