<?php
class VehiculeController {
    public function index() {
        $vehicules = Vehicule::findAll();
        $content = $this->render('vehicules', ['vehicules' => $vehicules]);
        $this->renderLayout($content);
    }

    public function show($id) {
        $vehicule = Vehicule::findById($id);
        if ($vehicule) {
            $content = $this->render('vehicules', ['vehicule' => $vehicule]);
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
