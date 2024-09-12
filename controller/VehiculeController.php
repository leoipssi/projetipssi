<?php
class VehiculeController extends BaseController {
    public function __construct($logger) {
        parent::__construct($logger);
        if (!Vehicule::isDbConnected()) {
            $this->logger->error("La connexion à la base de données n'est pas établie dans Vehicule.");
            throw new Exception("La connexion à la base de données n'est pas établie dans Vehicule.");
        }
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 9;
        
        try {
            $vehicules = Vehicule::findAll($page, $perPage);
            $totalVehicules = Vehicule::count();
            $totalPages = ceil($totalVehicules / $perPage);
            
            $this->render('vehicules/index', [
                'vehicules' => $vehicules,
                'currentPage' => $page,
                'totalPages' => $totalPages
            ]);
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de la récupération des véhicules : " . $e->getMessage());
            $this->renderError(500, "Une erreur est survenue lors de la récupération des véhicules.");
        }
    }

    public function show($id) {
        try {
            $vehicule = Vehicule::findById($id);
            if ($vehicule) {
                $offresActives = RentalOffer::findActiveByVehiculeId($id);
                
                $this->render('vehicules/show', [
                    'vehicule' => $vehicule,
                    'offresActives' => $offresActives
                ]);
            } else {
                $this->renderError(404);
            }
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de l'affichage du véhicule : " . $e->getMessage());
            $this->renderError(500, "Une erreur est survenue lors de l'affichage du véhicule.");
        }
    }

    public function create() {
        $this->requireAdmin();
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            try {
                $vehicule = Vehicule::create($data);
                if ($vehicule) {
                    $this->redirect('vehicules', ['success' => 'Véhicule ajouté avec succès']);
                } else {
                    $this->render('vehicules/create', ['error' => 'Erreur lors de la création du véhicule']);
                }
            } catch (Exception $e) {
                $this->logger->error("Erreur lors de la création du véhicule : " . $e->getMessage());
                $this->render('vehicules/create', ['error' => 'Une erreur est survenue lors de la création du véhicule.']);
            }
        } else {
            $this->render('vehicules/create');
        }
    }

    public function edit($id) {
        $this->requireAdmin();
        
        try {
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
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de la modification du véhicule : " . $e->getMessage());
            $this->renderError(500, "Une erreur est survenue lors de la modification du véhicule.");
        }
    }

    public function delete($id) {
        $this->requireAdmin();
        
        try {
            $vehicule = Vehicule::findById($id);
            if ($vehicule && $vehicule->delete()) {
                $this->redirect('vehicules', ['success' => 'Véhicule supprimé avec succès']);
            } else {
                $this->redirect('vehicules', ['error' => 'Erreur lors de la suppression du véhicule']);
            }
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de la suppression du véhicule : " . $e->getMessage());
            $this->redirect('vehicules', ['error' => 'Une erreur est survenue lors de la suppression du véhicule.']);
        }
    }
}
?>
