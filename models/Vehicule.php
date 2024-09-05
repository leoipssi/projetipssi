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
    private $categorie; // 'scooter' ou 'voiture'
    private $tarif_journalier;

    public function __construct($id, $type_id, $marque, $modele, $numero_serie, $couleur, $immatriculation, $kilometres, $date_achat, $prix_achat, $categorie, $tarif_journalier) {
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

    public static function create($data) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO vehicules (type_id, marque, modele, numero_serie, couleur, immatriculation, kilometres, date_achat, prix_achat, categorie, tarif_journalier) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            $data['tarif_journalier']
        ]);
        if ($stmt->rowCount() > 0) {
            return new Vehicule($conn->lastInsertId(), $data['type_id'], $data['marque'], $data['modele'], $data['numero_serie'], $data['couleur'], $data['immatriculation'], $data['kilometres'], $data['date_achat'], $data['prix_achat'], $data['categorie'], $data['tarif_journalier']);
        }
        return null;
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM vehicules");
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat'], $row['categorie'], $row['tarif_journalier']);
        }
        return $vehicules;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicules WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Vehicule($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat'], $row['categorie'], $row['tarif_journalier']);
        }
        return null;
    }

    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE vehicules SET type_id = ?, marque = ?, modele = ?, numero_serie = ?, couleur = ?, immatriculation = ?, kilometres = ?, date_achat = ?, prix_achat = ?, categorie = ?, tarif_journalier = ? WHERE id = ?");
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
            $this->id
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function count() {
        global $conn;
        $stmt = $conn->query("SELECT COUNT(*) FROM vehicules");
        return $stmt->fetchColumn();
    }

    public static function getRecentVehicules($limit = 5) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicules ORDER BY date_achat DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat'], $row['categorie'], $row['tarif_journalier']);
        }
        return $vehicules;
    }

    // Nouvelle méthode ajoutée
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
