<?php
class Vehicle {
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

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM vehicles");
        $vehicles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicles[] = new Vehicle($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat']);
        }
        return $vehicles;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Vehicle($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat']);
        }
        return null;
    }

    public static function getRecentVehicles($limit) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM vehicles ORDER BY date_achat DESC LIMIT ?");
        $stmt->execute([$limit]);
        $vehicles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicles[] = new Vehicle($row['id'], $row['type_id'], $row['marque'], $row['modele'], $row['numero_serie'], $row['couleur'], $row['immatriculation'], $row['kilometres'], $row['date_achat'], $row['prix_achat']);
        }
        return $vehicles;
    }

    public static function count() {
        global $conn;
        $stmt = $conn->query("SELECT COUNT(*) FROM vehicles");
        return $stmt->fetchColumn();
    }
}
