<?php
class RentalController {
    public function index() {
        $userId = $_SESSION['user_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10; // Nombre de locations par page

        $rentals = Rental::findByUserId($userId, $page, $perPage);
        $totalRentals = Rental::countByUserId($userId);
        $totalPages = ceil($totalRentals / $perPage);

        include 'views/rentals.php';
    }

    public function details($id) {
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $_SESSION['user_id']) {
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            $offer = RentalOffer::findById($rental->getOfferId());
            include 'views/rental_details.php';
        } else {
            // Gérer l'erreur
            include 'views/error.php';
        }
    }

    public function returnVehicle($id) {
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $_SESSION['user_id'] && $rental->getStatus() == 'En cours') {
            // Logique pour retourner le véhicule
            $rental->update(['status' => 'Terminée']);
            $vehicule = Vehicule::findById($rental->getVehiculeId());
            $vehicule->update(['disponible' => true]);
            
            // Générer la facture
            $this->generateInvoice($rental);

            header('Location: index.php?route=rentals');
            exit;
        } else {
            // Gérer l'erreur
            include 'views/error.php';
        }
    }

    public function invoice($id) {
        $rental = Rental::findById($id);
        if ($rental && $rental->getClientId() == $_SESSION['user_id'] && $rental->getStatus() == 'Terminée') {
            $invoice = Invoice::findByRentalId($rental->getId());
            if ($invoice) {
                include 'views/invoice.php';
            } else {
                // Gérer l'erreur
                include 'views/error.php';
            }
        } else {
            // Gérer l'erreur
            include 'views/error.php';
        }
    }

    private function generateInvoice($rental) {
        // Logique pour générer la facture
        $offer = RentalOffer::findById($rental->getOfferId());
        $amount = $offer->getPrix();
        
        $invoice = Invoice::create([
            'rental_id' => $rental->getId(),
            'montant' => $amount,
            'date_emission' => date('Y-m-d')
        ]);

        return $invoice;
    }
}
