<?php
require_once 'model/KorisniciModel.php';
require_once 'controller/KorisniciController.php';

$db = new PDO("pgsql:host=localhost;dbname=korisnicka_aplikacija", "postgres", "lozinka");
if (!$db) {
    die("Greška: Neuspješno povezivanje s bazom podataka");
}

$controller = new KorisniciController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['prijava'])) {
        $email = stripslashes($_REQUEST['email']);
        $lozinka = $_POST['lozinka'];
        $controller->prijava($email, $lozinka);
    }
}
?>

<head>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="text-center">
            <h1>Dobrodošli na aplikaciju za upravljanje korisnicima</h1>
            <a type="button" class="btn btn-outline-dark" style="font-weight: bold;" data-bs-toggle="modal"
                data-bs-target="#exampleModal">Prijava</a>
            <a type="button" href="view/RegistracijaView.php" class="btn btn-dark">Registracija</a>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Prijavi se</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-envelope"></i></span>
                            <input type="text" class="form-control" placeholder="Email" aria-label="Username" id="email"
                                name="email" aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control" placeholder="Lozinka" aria-label="Username"
                                id="lozinka" name="lozinka" aria-describedby="basic-addon1">
                        </div>
                        <div>
                            <input type="submit" class="btn btn-outline-primary" name="prijava" value="Nastavi" />
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <br><br>
</body>