<?php
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$controller = new KorisniciController();
$korisnikId = $_GET['id'] ?? null;

if ($korisnikId) {
    $controller->deaktivirajKorisnika($korisnikId);
}

?>
