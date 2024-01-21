<?php
include('main.php');
require_once '../model/NotifikacijeModel.php';
require_once '../controller/NotifikacijeController.php';

$controller = new NotifikacijeController();

$notifikacije = $controller->dohvatiNotifikacije();
$neprocitane = $controller->dohvatiNeprocitaneNotifikacije();

if (isset($_POST['oznaciSve'])) {
    $controller->oznaciSveNotifikacijeKaoProcitane();
    $notifikacije = $controller->dohvatiNotifikacije();
    $neprocitane = $controller->dohvatiNeprocitaneNotifikacije();
}

?>

<body>
    <div class="container" style="padding: 50px;">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#sveNotifikacije" style="color: black">Sve</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#neprocitaneNotifikacije" style="color: black">Nepročitane</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane container active" id="sveNotifikacije" style="padding: 20px">
                <?php foreach ($notifikacije as $notifikacija): ?>
                    <div class="card mb-3" style="width: 50%; margin-left: 25%;">
                        <div class="card-body">
                            <p>
                                <?php echo $notifikacija['sadrzaj']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <div class="tab-pane container fade" id="neprocitaneNotifikacije" style="padding: 20px">
                <form method="post" action="NotifikacijeView.php">
                    <button type="submit" class="btn btn-success" name="oznaciSve">Označi sve kao pročitano</button>
                </form>
                <?php foreach ($neprocitane as $neprocitano): ?>
                    <div class="card mb-3" style="width: 50%; margin-left: 25%">
                        <div class="card-body">
                            <p style="font-weight: bold">
                                <?php echo $neprocitano['sadrzaj']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>