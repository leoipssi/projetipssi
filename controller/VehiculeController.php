<?php
class VehiculeController extends BaseController {
    public function index() {
        $vehicules = Vehicule::findAll();
        $this->render('vehicules/index', ['vehicules' => $vehicules]);
    }

    public function show($id) {
        $vehicule = Vehicule::findById($id);
        if ($vehicule) {
            $this->render('vehicules/show', ['vehicule' => $vehicule]);
        } else {
            $this->renderError(404);
        }
    }

    public function create() {
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            $vehicule = Vehicule::create($data);
            if ($vehicule) {
                $this->redirect('vehicules', ['success' => 'Véhicule ajouté avec succès']);
            } else {
                $this->render('vehicules/create', ['error' => 'Erreur lors de la création du véhicule']);
            }
        } else {
            $this->render('vehicules/create');
        }
    }

    public function edit($id) {
        $this->requireAdmin();
        
        $vehicule = Vehicule::findById($id);
        if (!$vehicule) {
            $this->renderError(404);
            return;
        }

        if ($this->isPost()) {
            $data = $this->getPostData();
            if ($vehicule->update($data)) {
                $this->redirect('vehicules', ['success' => 'Véhicule mis à jour avec succès']);
            } else {
                $this->render('vehicules/edit', ['vehicule' => $vehicule, 'error' => 'Erreur lors de la mise à jour du véhicule']);
            }
        } else {
            $this->render('vehicules/edit', ['vehicule' => $vehicule]);
        }
    }

    public function delete($id) {
        $this->requireAdmin();
        
        $vehicule = Vehicule::findById($id);
        if ($vehicule && $vehicule->delete()) {
            $this->redirect('vehicules', ['success' => 'Véhicule supprimé avec succès']);
        } else {
            $this->redirect('vehicules', ['error' => 'Erreur lors de la suppression du véhicule']);
        }
    }
}
