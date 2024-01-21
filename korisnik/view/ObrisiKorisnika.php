<?php
include('main.php');
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$controller = new KorisniciController();

$id = $_GET['id'];
$korisnik = $controller->dohvatiDetalje($id);

if (isset($_POST['obrisi'])) {
    $korisnici = $controller->izbrisiKorisnika($id);
}

?>

<body>
    <div class="container" style="align-items: center; justify-content: center; height: 90vh; display: flex;">
        <div class="card">
            <div class="card-header">
                Obriši korisnika
                <?php echo $korisnik['ime'] . ' ' . $korisnik['prezime'] ?>
            </div>
            <div class="card-body">
                <p>Jeste li sigurni da želite obrisati korisnika
                    <?php echo $korisnik['ime'] . ' ' . $korisnik['prezime'] ?>?</p>
                <form method="post" action="" style="background-color: transparent">
                    <a href="KorisniciTable.php"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                    <button type="submit" class="btn btn-outline-primary" name="obrisi">Obriši</button>
                </form>
            </div>
        </div>
    </div>
</body>