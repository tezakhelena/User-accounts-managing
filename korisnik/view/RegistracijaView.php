<?php
require_once '../model/KorisniciModel.php';
require_once '../controller/KorisniciController.php';

$controller = new KorisniciController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registracija'])) {
        $ime = $_POST['ime'];
        $prezime = $_POST['prezime'];
        $lozinka = $_POST['lozinka'];
        $email = $_POST['email'];
        $vrsta_korisnika_id = 2;
        $omiljeniHobi = isset($_POST['omiljeni_hobi']) ? $_POST['omiljeni_hobi'] : [];
        $controller->registracija($ime, $prezime, $lozinka, $email, $omiljeniHobi, $vrsta_korisnika_id);
    }
}
?>
<head>
    <link rel="stylesheet" href="../style.css">
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
    <div class="container" style="align-items: center; justify-content: center; height: 100vh; display: flex;">
        <div class="card" style="width: 40%;">
            <div class="card-header">
                Registracija
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-regular fa-user"></i></span>
                        <input type="text" class="form-control" placeholder="Ime" aria-label="Ime"
                            aria-describedby="basic-addon1" name="ime" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-user"></i></span>
                        <input type="text" class="form-control" placeholder="Prezime" aria-label="Prezime"
                            aria-describedby="basic-addon1" name="prezime" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" class="form-control" placeholder="Email" aria-label="Email"
                            aria-describedby="basic-addon1" name="email" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" placeholder="Lozinka" aria-label="Lozinka"
                            aria-describedby="basic-addon1" name="lozinka" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Omiljeni hobiji</label>
                        <div id="hobijiContainer">
                        </div>
                        <button type="button" class="btn btn-outline-primary" onclick="dodajPoljeZaHobi()"><i
                                class="fa-solid fa-plus"></i></button>
                    </div>
                    <button type="submit" class="btn btn-outline-primary" name="registracija">Registriraj se</button>
                </form>
            </div>
        </div>
    </div>
</body>

<script>
    function dodajPoljeZaHobi() {
        var container = document.getElementById('hobijiContainer');
        var input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control mb-3';
        input.name = 'omiljeni_hobi[]';
        input.placeholder = 'Unesite hobi';
        container.appendChild(input);
    }
</script>