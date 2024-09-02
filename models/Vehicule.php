// e-motion/models/Vehicle.php
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

    // Getters et setters existants...

    public function save() {
        global $conn;
        if ($this->id) {
            // Update existing record
            $sql = "UPDATE vehicles SET type_id = ?, marque = ?, modele = ?, numero_serie = ?, couleur = ?, immatriculation = ?, kilometres = ?, date_achat = ?, prix_achat = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$this->type_id, $this->marque, $this->modele, $this->numero_serie, $this->couleur, $this->immatriculation, $this->kilometres, $this->date_achat, $this->prix_achat, $this->id]);
        } else {
            // Insert new record
            $sql = "INSERT INTO vehicles (type_id, marque, modele, numero_serie, couleur, immatriculation, kilometres, date_achat, prix_achat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$this->type_id, $this->marque, $this->modele, $this->numero_serie, $this->couleur, $this->immatriculation, $this->kilometres, $this->date_achat, $this->prix_achat]);
            $this->id = $conn->lastInsertId();
        }
    }

    public function delete() {
        global $conn;
        $sql = "DELETE FROM vehicles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$this->id]);
    }

    public static function findById($id) {
        global $conn;
        $sql = "SELECT * FROM vehicles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return new Vehicle(
                $result['id'],
                $result['type_id'],
                $result['marque'],
                $result['modele'],
                $result['numero_serie'],
                $result['couleur'],
                $result['immatriculation'],
                $result['kilometres'],
                $result['date_achat'],
                $result['prix_achat']
            );
        }
        return null;
    }

    public static function findAll() {
        global $conn;
        $sql = "SELECT * FROM vehicles";
        $stmt = $conn->query($sql);
        $vehicles = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vehicles[] = new Vehicle(
                $row['id'],
                $row['type_id'],
                $row['marque'],
                $row['modele'],
                $row['numero_serie'],
                $row['couleur'],
                $row['immatriculation'],
                $row['kilometres'],
                $row['date_achat'],
                $row['prix_achat']
            );
        }
        return $vehicles;
    }
}
