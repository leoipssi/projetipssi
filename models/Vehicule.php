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

    public function __construct($id, $type_id, $marque, $modele, $numero_serie, $couleur, $immatriculation, $kilometres, $date_achat, $prix_achat) {
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

    public function getType() {
        global $conn;
        $stmt = $conn->prepare("SELECT nom FROM vehicle_types WHERE id = ?");
        $stmt->execute([$this->type_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nom'] : 'Type inconnu';
    }

    public static function create($data) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO vehicules (type_id, marque, modele, numero_serie, couleur, immatriculation, kilometres, date_achat, prix_achat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['type_id'],
            $data['marque'],
            $data['modele'],
            $data['numero_serie'],
            $data['couleur'],
            $data['immatriculation'],
            $data['kilometres'],
            $data['date_achat'],
            $data['prix_achat']
        ]);
        if ($stmt->rowCount() > 0) {
            return new Vehicule($conn->lastInsertId(), $data['type_id'], $data['marque'], $data['modele'], $data['numero_serie'], $data['couleur'], $data['immatriculation'], $data['kilometres'], $data['date_achat'], $data['prix_achat']);
        }
        return null;
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM vehicules");
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat']);
        }
        return $vehicules;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicules WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Vehicule($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat']);
        }
        return null;
    }

    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE vehicules SET type_id = ?, marque = ?, modele = ?, numero_serie = ?, couleur = ?, immatriculation = ?, kilometres = ?, date_achat = ?, prix_achat = ? WHERE id = ?");
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
        $stmt = $conn->prepare("SELECT * FROM vehicules ORDER BY date_achat DESC LIMIT ?");
        $stmt->execute([$limit]);
        $vehicules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicules[] = new Vehicule($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat']);
        }
        return $vehicules;
    }
}
