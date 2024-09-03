<?php
// Assurez-vous que l'utilisateur est connecté et a le droit de voir cette facture
if (!$this->isLoggedIn() || $rental->getClientId() != $this->getCurrentUser()->getId()) {
    $this->redirect('rentals');
}
?>

<div class="invoice-container">
    <h1>Facture #<?= $invoice->getId() ?></h1>
    
    <div class="invoice-details">
        <p><strong>Date d'émission:</strong> <?= htmlspecialchars($invoice->getDateEmission()) ?></p>
        <p><strong>Location #:</strong> <?= $rental->getId() ?></p>
    </div>

    <div class="client-details">
        <h2>Client</h2>
        <p><?= htmlspecialchars($this->getCurrentUser()->getNom() . ' ' . $this->getCurrentUser()->getPrenom()) ?></p>
        <p><?= htmlspecialchars($this->getCurrentUser()->getAdresse()) ?></p>
        <p><?= htmlspecialchars($this->getCurrentUser()->getEmail()) ?></p>
    </div>

    <div class="rental-details">
        <h2>Détails de la location</h2>
        <p><strong>Véhicule:</strong> <?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></p>
        <p><strong>Date de début:</strong> <?= htmlspecialchars($rental->getDateDebut()) ?></p>
        <p><strong>Date de fin:</strong> <?= htmlspecialchars($rental->getDateFin()) ?></p>
        <p><strong>Durée:</strong> <?= htmlspecialchars($offer->getDuree()) ?> jours</p>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Location <?= htmlspecialchars($vehicule->getMarque() . ' ' . $vehicule->getModele()) ?></td>
                <td><?= htmlspecialchars($offer->getDuree()) ?> jours</td>
                <td><?= htmlspecialchars(number_format($offer->getPrix(), 2)) ?> €</td>
                <td><?= htmlspecialchars(number_format($invoice->getMontant(), 2)) ?> €</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td><strong><?= htmlspecialchars(number_format($invoice->getMontant(), 2)) ?> €</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="invoice-footer">
        <p>Merci d'avoir choisi notre service de location de véhicules.</p>
        <p>Pour toute question concernant cette facture, veuillez nous contacter à facturation@e-motion.com</p>
    </div>
</div>

<a href="<?= $this->url('rentals') ?>" class="btn btn-secondary">Retour à mes locations</a>

<style>
    .invoice-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .invoice-details, .client-details, .rental-details {
        margin-bottom: 20px;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }
    .invoice-table th, .invoice-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    .invoice-table th {
        background-color: #f8f9fa;
    }
    .invoice-footer {
        margin-top: 20px;
        font-size: 0.9em;
        color: #6c757d;
    }
    .btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 20px;
    }
</style>
