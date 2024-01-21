<?php
include('main.php');
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$controller = new KorisniciController();
$korisnici = $controller->prikaziSveKorisnike();

if (isset($_GET['obrisi'])) {
    $obrisi_id = $_GET['obrisi'];
    $controller->izbrisiKorisnika($obrisi_id);
    header("Location: KorisniciTable.php");
    exit();
}

if ($korisnici) {
    ?>

    <body>
        <div class="container mt-5">
            <h1>Pregled korisnika</h1>

            <table class="table table-light table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th>Email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($korisnici as $korisnik): ?>
                        <tr>
                            <td>
                                <?php echo $korisnik['id']; ?>
                            </td>
                            <td>
                                <?php echo $korisnik['ime']; ?>
                            </td>
                            <td>
                                <?php echo $korisnik['prezime']; ?>
                            </td>
                            <td>
                                <?php echo $korisnik['email']; ?>
                            </td>
                            <td>

                                <a class="btn btn-sm btn-outline-secondary"
                                    href="DetaljiKorisnika.php?id=<?php echo $korisnik['id'] ?>"><i
                                        class="fa-solid fa-circle-info"></i></a>

                                <a class="btn btn-sm btn-outline-secondary"
                                    href="UrediKorisnika.php?id=<?php echo $korisnik['id'] ?>"><i
                                        class="fa-regular fa-pen-to-square"></i></a>

                                <a class="btn btn-sm btn-outline-secondary" href="#"
                                    onclick="obrisiKorisnika(<?php echo $korisnik['id']; ?>)"><i class="fa-solid fa-trash"></i></a>

                                <?php if ($korisnik['aktivan'] === true) { ?>
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="DeaktivirajKorisnika.php?id=<?php echo $korisnik['id'] ?>"><i
                                            class="fa-solid fa-user-slash"></i></a>
                                <?php } ?>
                                <?php if ($korisnik['aktivan'] === false) { ?>
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="AktivirajKorisnika.php?id=<?php echo $korisnik['id'] ?>"><i
                                            class="fa-solid fa-lock-open"></i></a>
                                <?php } ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>

    </html>
    <?php
}
?>

<script>
    function obrisiKorisnika(korisnik_id) {
        var confirmed = confirm("Jeste li sigurni da želite obrisati korisnika?");

        if (confirmed) {
            window.location.href = `KorisniciTable.php?obrisi=${korisnik_id}`;
        }
        header("Location: KorisniciTable.php");
        die();
    }

</script>