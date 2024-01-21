<?php
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$controller = new KorisniciController();
$korisnikId = $_GET['id'];

if ($korisnikId) {
    $controller->aktivirajKorisnika($korisnikId);
}

?>
