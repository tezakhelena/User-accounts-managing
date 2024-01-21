<!-- UrediKorisnikaView.php -->

<?php
include('main.php');
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$controller = new KorisniciController();

$id = $_GET['id'];
$korisnik = $controller->dohvatiDetalje($id);

if (isset($_POST['uredi'])) {
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $korisnicko_ime = $_POST['korisnicko_ime'];
    $email = $_POST['email'];

    $korisnici = $controller->azurirajKorisnika($id, $ime, $prezime, $korisnicko_ime, $email);
}

if ($korisnik) {
    ?>

    <body>

        <div class="container" style="align-items: center; justify-content: center; height: 100vh; display: flex;">
            <div class="card" style="width: 40%;">
                <div class="card-header">
                    Ažuriraj korisnika
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Ime" aria-label="Ime"
                                aria-describedby="basic-addon1" name="ime" value="<?php echo $korisnik['ime'] ?>" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Prezime" aria-label="Prezime"
                                aria-describedby="basic-addon1" name="prezime" value="<?php echo $korisnik['prezime'] ?>" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Korisničko ime" aria-label="Korisničko ime"
                                aria-describedby="basic-addon1" name="korisnicko_ime" value="<?php echo $korisnik['korisnicko_ime'] ?>" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Email" aria-label="Email"
                                aria-describedby="basic-addon1" name="email" value="<?php echo $korisnik['email'] ?>" required>
                        </div>

                        <a href="KorisniciTable.php"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                        <button type="submit" class="btn btn-outline-primary" name="uredi">Ažuriraj</button>
                    </form>
                </div>
            </div>
        </div>
    </body>

    </html>
<?php } ?>