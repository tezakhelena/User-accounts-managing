<?php
include('main.php');
require_once '../model/PostoviModel.php';
require_once '../controller/PostoviController.php';

$post_id = $_GET['post_id'];


$controller = new PostoviController();
$komentari = $controller->dohvatiKomentare($post_id);
$post = $controller->dohvatiPost($post_id);


if (isset($_POST['kreirajKomentar'])) {
    $sadrzaj = $_POST['sadrzaj'];
    $post_id = $_GET['post_id'];
    $controller->kreirajKomentar($post_id, $sadrzaj);
    header("Location: DetaljiPosta.php?post_id=$post_id");
}

if (isset($_POST['kreirajOdgovor'])) {
    $komentar_id = $_POST['komentar_id'];
    $odgovorTekst = $_POST['odgovorTekst'];
    $controller->kreirajOdgovor($komentar_id, $odgovorTekst);
    $post = $controller->dohvatiPost($post_id);
}

if (isset($_GET['obrisi']) && isset($_GET['post_id'])) {
    $obrisi_id = $_GET['obrisi'];
    $post_id = $_GET['post_id'];
    $controller->obrisiKomentar($obrisi_id);
    header("Location: DetaljiPosta.php?post_id=$post_id");
    exit();
}

if (isset($_GET['obrisiOdgovor']) && isset($_GET['post_id'])) {
    $obrisi_id = $_GET['obrisiOdgovor'];
    $post_id = $_GET['post_id'];
    $controller->obrisiOdgovor($obrisi_id);
    header("Location: DetaljiPosta.php?post_id=$post_id");
    exit();
}


?>
<div class="container" style="padding: 20px;">
    <div class="accordion" id="accordionExample" style="width: 50%; margin-left: 30%">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                    aria-expanded="true" aria-controls="collapseOne">
                    <div class="d-flex flex-column ">
                        <span style="font-weight: bold; font-size: 15px">
                            <?php echo $post['korisnicko_ime'] ?>
                        </span>
                        <span style="font-size: 13px;">
                            <?php echo $post['datum_objave'] ?>
                        </span>
                        <span style="font-weight: bold; font-size: 30px">
                            <?php echo $post['naslov'] ?>
                        </span>
                        <span>
                            <?php echo $post['sadrzaj'] ?>
                        </span>
                    </div>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <?php if ($komentari) { ?>
                        <div>
                            <?php foreach ($komentari as $kom): ?>
                                <?php $odgovori = $controller->dohvatiOdgovore($kom['komentar_id']); ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span style="font-weight: bold;">
                                        <?php echo $kom['korisnicko_ime'] ?>
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="sadrzaj">
                                        <?php echo $kom['sadrzaj']; ?>
                                    </span>

                                    <div class="d-flex">
                                        <space>
                                            <?php if ($_SESSION['aktivan'] == true) { ?>
                                                <a href="#" class="btn btn-sm btn-outline-secondary"
                                                    onclick="prikaziPoljeZaOdgovor(<?php echo $kom['komentar_id']; ?>)"><i
                                                        class="fa-solid fa-reply"></i></a>
                                            <?php } ?>
                                            <?php if ($_SESSION['vrsta_korisnika_id'] === 1 || $kom['korisnik_id'] == $_SESSION['id']) { ?>
                                                <a href="#" class="btn btn-sm btn-outline-secondary"
                                                    onclick="obrisiKomentar(<?php echo $kom['komentar_id']; ?>, <?php echo $post_id; ?>)"><i
                                                        class="fa-solid fa-trash"></i></a>
                                            <?php } ?>
                                            <?php if ($odgovori) { ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    onclick="prikaziOdgovore(<?php echo $kom['komentar_id']; ?>)"><i
                                                        class="fa-solid fa-chevron-down"></i></button>
                                            <?php } ?>

                                        </space>
                                    </div>
                                </div>


                                <?php if ($odgovori) { ?>
                                    <div class="mb-3" id="odgovoriContainer<?php echo $kom['komentar_id']; ?>"
                                        style="display: none">
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($odgovori as $odgovor): ?>
                                                <li class="list-group-item">
                                                    <div class="d-flex flex-column align-items-end">
                                                        <span style="font-weight: bold;">
                                                            <?php echo $odgovor['korisnicko_ime'] ?>
                                                        </span>
                                                        <span>
                                                            <?php echo $odgovor['sadrzaj']; ?>
                                                            <?php if ($_SESSION['vrsta_korisnika_id'] === 1 || $odgovor['korisnik_id'] == $_SESSION['id']) { ?>
                                                                <button class="btn btn-sm btn-outline-danger"
                                                                    onclick="obrisiOdgovor(<?php echo $odgovor['odgovor_id']; ?>, <?php echo $post_id; ?>)">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            <?php } ?>
                                                        </span>
                                                        <span class="mt-2" style="font-size: 11px;">
                                                            <?php echo $odgovor['datum_odgovora']; ?>
                                                        </span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php } else {
                                    echo "<p class='text-end'>Nema odgovora</p>";
                                } ?>


                                <div class="mb-3" id="poljeZaOdgovor<?php echo $kom['komentar_id']; ?>" style="display: none;">
                                    <form method="post">
                                        <div class="mb-2">
                                            <textarea class="form-control" name="odgovorTekst" placeholder="Odgovor"></textarea>
                                        </div>
                                        <input type="hidden" name="komentar_id" value="<?php echo $kom['komentar_id']; ?>">

                                        <button type="submit" class="btn btn-sm btn-primary" name="kreirajOdgovor">Spremi
                                            odgovor</button>
                                    </form>
                                </div>

                                <hr />

                            <?php endforeach; ?>
                        </div>
                    <?php } else {
                        echo "<p>Nema komentara</p>";
                    } ?>
                    <form method="post" style="margin-top: 10px;">
                        <div id="komentarContainer">
                        </div>
                        <button type="submit" class="btn btn-primary" name="kreirajKomentar" id="spremiKomentar"
                            style="display: none;">Spremi komentar</button>
                            <?php if ($_SESSION['aktivan'] == true) { ?>
                        <button type="button" class="btn btn-outline-primary" onclick="dodajPoljeZaKomentar()">Dodaj
                            komentar</button>
                            <?php }?>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>



<script>
    function prikaziPoljeZaOdgovor(komentarId) {
        var poljeZaOdgovor = document.getElementById('poljeZaOdgovor' + komentarId);
        poljeZaOdgovor.style.display = 'block';
    }

    function prikaziOdgovore(komentarId) {
        var odgovoriContainer = document.getElementById('odgovoriContainer' + komentarId);

        if (odgovoriContainer.style.display === 'none') {
            odgovoriContainer.style.display = 'block';
        } else {
            odgovoriContainer.style.display = 'none';
        }

        return false;
    }

    function dodajPoljeZaKomentar() {
        var container = document.getElementById('komentarContainer');
        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control mb-3';
        input.name = 'sadrzaj';
        input.placeholder = 'Unesite komentar';
        container.appendChild(input);

        document.getElementById('spremiKomentar').style.display = 'block';
    }

    function obrisiKomentar(komentarId, postId) {
        var confirmed = confirm("Jeste li sigurni da želite obrisati ovaj komentar?");

        if (confirmed) {
            window.location.href = `DetaljiPosta.php?obrisi=${komentarId}&post_id=${postId}`;
        }
    }

    function obrisiOdgovor(odgovorId, postId) {
        var confirmed = confirm("Jeste li sigurni da želite obrisati ovaj odgovor?");

        if (confirmed) {
            window.location.href = `DetaljiPosta.php?obrisiOdgovor=${odgovorId}&post_id=${postId}`;
        }
    }
</script>