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
        if (!$db instanceof PDO) {
            throw new Exception("L'objet de base de données fourni n'est pas une instance valide de PDO.");
        }
        self::$db = $db;
        error_log("Connexion à la base de données établie pour la classe Vehicule");
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
        try {
            $stmt = self::$db->prepare("SELECT nom FROM vehicule_types WHERE id = ?");
            $stmt->execute([$this->type_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['nom'] : 'Type inconnu';
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du type de véhicule : " . $e->getMessage());
            throw new Exception("Impossible de récupérer le type de véhicule.");
        }
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

        try {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du véhicule : " . $e->getMessage());
            throw new Exception("Impossible de créer le véhicule.");
        }
        return null;
    }

    public function update($data) {
        self::checkDbConnection();
        try {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du véhicule : " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour le véhicule.");
        }
    }

    public static function findAll($page = 1, $perPage = 10) {
        self::checkDbConnection();
        $offset = ($page - 1) * $perPage;
        try {
            $stmt = self::$db->prepare("SELECT * FROM vehicules LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de tous les véhicules : " . $e->getMessage());
            throw new Exception("Impossible de récupérer la liste des véhicules.");
        }
    }

    public static function findById($id) {
        self::checkDbConnection();
        try {
            $stmt = self::$db->prepare("SELECT * FROM vehicules WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                // Vérifions chaque champ pour nous assurer qu'il n'y a pas de tableau inattendu
                foreach ($row as $key => $value) {
                    if (is_array($value)) {
                        error_log("Champ inattendu de type array dans vehicules pour l'ID $id : $key");
                        $row[$key] = json_encode($value); // Convertir le tableau en JSON si nécessaire
                    }
                }
                
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
            } else {
                error_log("Aucun véhicule trouvé avec l'ID : $id");
                return null;
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche du véhicule par ID : " . $e->getMessage());
            throw new Exception("Impossible de trouver le véhicule spécifié.");
        } catch (Exception $e) {
            error_log("Erreur inattendue lors de la recherche du véhicule par ID : " . $e->getMessage());
            throw $e;
        }
    }

    public static function findAvailable() {
        self::checkDbConnection();
        try {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche des véhicules disponibles : " . $e->getMessage());
            throw new Exception("Impossible de récupérer la liste des véhicules disponibles.");
        }
    }

    public static function findRented() {
        self::checkDbConnection();
        try {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche des véhicules loués : " . $e->getMessage());
            throw new Exception("Impossible de récupérer la liste des véhicules loués.");
        }
    }

    public static function count() {
        self::checkDbConnection();
        try {
            $stmt = self::$db->query("SELECT COUNT(*) FROM vehicules");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des véhicules : " . $e->getMessage());
            throw new Exception("Impossible de compter le nombre de véhicules.");
        }
    }

    public static function getRecentVehicules($limit = 5) {
        self::checkDbConnection();
        try {
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des véhicules récents : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les véhicules récents.");
        }
    }

public static function getTopRented($limit = 5) {
        self::checkDbConnection();
        try {
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
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traitement des résultats pour s'assurer qu'il n'y a pas de tableaux
            foreach ($results as &$row) {
                foreach ($row as $key => $value) {
                    if (is_array($value)) {
                        $row[$key] = json_encode($value);
                    }
                }
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des véhicules les plus loués : " . $e->getMessage());
            throw new Exception("Impossible de récupérer les véhicules les plus loués.");
        }
    }

    private static function checkDbConnection() {
        if (!self::$db instanceof PDO) {
            error_log("La connexion à la base de données n'est pas établie dans la classe Vehicule");
            throw new Exception("La connexion à la base de données n'est pas établie. Assurez-vous d'appeler Vehicule::setDB() avec une instance PDO valide avant d'utiliser la classe.");
        }
    }

    public function delete() {
        self::checkDbConnection();
        try {
            $stmt = self::$db->prepare("DELETE FROM vehicules WHERE id = ?");
            $stmt->execute([$this->id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du véhicule : " . $e->getMessage());
            throw new Exception("Impossible de supprimer le véhicule.");
        }
    }

    public static function search($criteria) {
        self::checkDbConnection();
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
            $stmt = self::$db->prepare($sql);
            $stmt->execute($params);
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
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de véhicules : " . $e->getMessage());
            throw new Exception("Impossible de rechercher les véhicules.");
        }
    }
}
?>
