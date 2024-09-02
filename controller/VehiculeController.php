<?php
class VehicleController {
    public function index() {
        $vehicles = Vehicle::findAll();
        $content = $this->render('vehicles', ['vehicles' => $vehicles]);
        $this->renderLayout($content);
    }

    public function show($id) {
        $vehicle = Vehicle::findById($id);
        if ($vehicle) {
            $content = $this->render('vehicle_details', ['vehicle' => $vehicle]);
            $this->renderLayout($content);
        } else {
            header('HTTP/1.0 404 Not Found');
            $content = $this->render('404');
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
