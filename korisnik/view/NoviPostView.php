<?php
require_once('main.php');
require_once '../model/PostoviModel.php';
require_once '../controller/PostoviController.php';

$controller = new PostoviController();

if (isset($_POST['post'])) {
    $korisnik_id = $_SESSION['id'];
    $naslov = $_POST['naslov'];
    $sadrzaj = $_POST['sadrzaj'];

    $controller->kreirajPost($korisnik_id, $naslov, $sadrzaj);
}

?>

<body>
    <div class="container" style="align-items: center; justify-content: center; height: 100vh; display: flex;">
        <div class="card" style="width: 40%;">
            <div class="card-header">
                Novi post
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Naslov objave" aria-label="Naslov"
                            aria-describedby="basic-addon1" name="naslov" required>
                    </div>
                    <div class="input-group mb-3">
                        <textarea class="form-control" placeholder="Sadržaj" name="sadrzaj" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary" name="post">Objavi</button>
                </form>
            </div>
        </div>
    </div>
</body>