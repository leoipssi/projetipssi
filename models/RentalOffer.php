return null;
    }

    public function update($data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE location_offers SET vehicule_id = ?, duree = ?, kilometres = ?, prix = ?, is_active = ?, is_available = ? WHERE id = ?");
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
    }

    public static function getActiveOffers() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM location_offers WHERE is_active = TRUE AND is_available = TRUE");
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
    }

    public function delete() {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM location_offers WHERE id = ?");
        $stmt->execute([$this->id]);
        return $stmt->rowCount() > 0;
    }

    public function hasActiveRentals() {
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rentals WHERE offer_id = ? AND status = 'En cours'");
        $stmt->execute([$this->id]);
        return $stmt->fetchColumn() > 0;
    }

    public static function findAvailable() {
        global $conn;
        $stmt = $conn->query("SELECT * FROM location_offers WHERE is_active = TRUE AND is_available = TRUE");
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
    }

    public static function findActiveByVehiculeId($vehiculeId) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM location_offers WHERE vehicule_id = ? AND is_active = TRUE AND is_available = TRUE");
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
    }

    public function toggleActive() {
        $this->is_active = !$this->is_active;
        return $this->update(['is_active' => $this->is_active]);
    }

    public function hide() {
        return $this->update(['is_active' => false]);
    }
}
