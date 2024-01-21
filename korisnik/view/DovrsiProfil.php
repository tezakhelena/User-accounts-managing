<?php
require('main.php');
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$controller = new KorisniciController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dovrsetakProfila'])) {
        $korisnik_id = $_SESSION['temp_korisnik_id'];
        $adresa = $_POST['adresa'];
        $broj_telefona = $_POST['broj_telefona'];
        $rodjendan = $_POST['rodjendan'];
        $formatiraniRodjendan = date('Y-m-d', strtotime($rodjendan));
        $spol = $_POST['spol'];
        $biografija = $_POST['biografija'];

        $controller->dovrsetakProfila($korisnik_id, $adresa, $broj_telefona, $formatiraniRodjendan, $spol, $biografija);
    }
}

?>

<body>
    <div class="container" style="align-items: center; justify-content: center; height: 90vh; display: flex;">
        <div class="card" style="width: 40%">
            <div class="card-header">Dovrši profil</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Adresa" aria-label="Adresa"
                            aria-describedby="basic-addon1" name="adresa" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Broj telefona" aria-label="Broj telefona"
                            aria-describedby="basic-addon1" name="broj_telefona" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="date" class="form-control" placeholder="Datum rođenja" aria-label="Datum rođenja"
                            aria-describedby="basic-addon1" name="rodjendan" required>
                    </div>
                    <div class="input-group mb-3">
                        <label>Spol: </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="spolM" name="spol" value="Muški">
                            <label class="form-check-label" for="spolM">Muški</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="spolZ" name="spol" value="Ženski">
                            <label class="form-check-label" for="spolZ">Ženski</label>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <textarea class="form-control" id="biografija" name="biografija" rows="5"
                            placeholder="Unesite svoju biografiju"></textarea>
                    </div>

                    <input type="submit" name="dovrsetakProfila" value="Dovrši profil">
                </form>
            </div>
        </div>
    </div>
</body>