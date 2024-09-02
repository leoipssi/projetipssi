<?php
class Client {
    private $id;
    private $user_id;
    private $nom;
    private $prenom;
    private $date_naissance;
    private $adresse;
    private $telephone;
    private $numero_permis;

    public function __construct($id, $user_id, $nom, $prenom, $date_naissance, $adresse, $telephone, $numero_permis) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->adresse = $adresse;
        $this->telephone = $telephone;
        $this->numero_permis = $numero_permis;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getDateNaissance() { return $this->date_naissance; }
    public function getAdresse() { return $this->adresse; }
    public function getTelephone() { return $this->telephone; }
    public function getNumeroPermis() { return $this->numero_permis; }

    public static function createClient($userData, $clientData) {
        global $conn;
        
        // Créer d'abord l'utilisateur
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$userData['username'], $userData['email'], password_hash($userData['password'], PASSWORD_DEFAULT)]);
        $userId = $conn->lastInsertId();

        if ($userId) {
            // Ensuite, créer le client
            $stmt = $conn->prepare("INSERT INTO clients (user_id, nom, prenom, date_naissance, adresse, telephone, numero_permis) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $clientData['nom'],
                $clientData['prenom'],
                $clientData['date_naissance'],
                $clientData['adresse'],
                $clientData['telephone'],
                $clientData['numero_permis']
            ]);
            
            if ($stmt->rowCount() > 0) {
                return new Client(
                    $conn->lastInsertId(),
                    $userId,
                    $clientData['nom'],
                    $clientData['prenom'],
                    $clientData['date_naissance'],
                    $clientData['adresse'],
                    $clientData['telephone'],
                    $clientData['numero_permis']
                );
            }
        }
        return null;
    }

    public static function findById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Client(
                $row['id'],
                $row['user_id'],
                $row['nom'],
                $row['prenom'],
                $row['date_naissance'],
                $row['adresse'],
                $row['telephone'],
                $row['numero_permis']
            );
        }
        return null;
    }

    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE clients SET nom = ?, prenom = ?, date_naissance = ?, adresse = ?, telephone = ?, numero_permis = ? WHERE id = ?");
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['date_naissance'],
            $data['adresse'],
            $data['telephone'],
            $data['numero_permis'],
            $this->id
        ]);
        return $stmt->rowCount() > 0;
    }

    public static function findAll() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM clients");
        $clients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clients[] = new Client(
                $row['id'],
                $row['user_id'],
                $row['nom'],
                $row['prenom'],
                $row['date_naissance'],
                $row['adresse'],
                $row['telephone'],
                $row['numero_permis']
            );
        }
        return $clients;
    }
}
