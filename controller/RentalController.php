<?php
class RentalController {
    public function index() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }
        $userId = $_SESSION['user_id'];
        $rentals = Rental::findByUserId($userId);
        $content = $this->render('rentals', ['rentals' => $rentals]);
        $this->renderLayout($content);
    }

    public function create($vehicleId, $startDate, $endDate) {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }
        $userId = $_SESSION['user_id'];
        $rental = Rental::create($userId, $vehicleId, $startDate, $endDate);
        if ($rental) {
            header('Location: index.php?route=rentals');
        } else {
            $error = "Erreur lors de la création de la location";
            $content = $this->render('rental_form', ['error' => $error]);
            $this->renderLayout($content);
        }
    }

    private function render($view, $data = []) {
        extract($data);
        ob_start();
        include "views/{$view}.php";
        return ob_get_clean();
    }

    private function renderLayout($content) {
        include 'views/layouts/main.php';
    }
}
