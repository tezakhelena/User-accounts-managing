<?php
include('main.php');
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$db = new PDO("pgsql:host=localhost;dbname=korisnicka_aplikacija", "postgres", "lozinka");
if (!$db) {
    die("Greška: Neuspješno povezivanje s bazom podataka");
}

$controller = new KorisniciController();
$id = $_GET['id'];

$korisnik = $controller->dohvatiDetalje($id);
$profil = $controller->dohvatiProfilKorisnika($id);
$dob = $controller->dohvatiDobKorisnika($id);
?>


<!DOCTYPE html>
<html>

<body>
    <div class="container mt-5">
        <div class="card mx-auto" style="width: 30rem; text-align: left">
            <div class="card-header">
                Detalji korisnika
            </div>
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">
                    <?php echo $korisnik['korisnicko_ime'] ?>
                </h6>
                <div class="card-body">
                    <p><strong>Ime:</strong>
                        <?php echo $korisnik['ime'] ?>
                    </p>
                    <p><strong>Prezime:</strong>
                        <?php echo $korisnik['prezime'] ?>
                    </p>
                    <p><strong>Korisničko ime:</strong>
                        <?php echo $korisnik['korisnicko_ime'] ?>
                    </p>
                    <p><strong>Email:</strong>
                        <?php echo $korisnik['email'] ?>
                    </p>
                    <p><strong>Omiljeni hobiji:</strong>
                        <?php echo $korisnik['omiljeni_hobi'] ?>
                    </p>
                    <p><strong>Datum registracije:</strong>
                        <?php echo $korisnik['datum_registracije'] ?>
                    </p>
                    <p><strong>Starost:</strong>
                        <?php echo $dob['dob'] ?>
                    </p>
                    <p><strong>Spol:</strong>
                        <?php echo $profil['spol'] ?>
                    </p>
                    <p><strong>Biografija:</strong>
                        <?php echo $profil['biografske_informacije'] ?>
                    </p>
                    <a href="KorisniciTable.php"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>