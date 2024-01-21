<?php
include('main.php');
require_once '../model/PostoviModel.php';
require_once '../controller/PostoviController.php';

$controller = new PostoviController();
$postovi = $controller->dohvatiPostove();
$mojiPostovi = $controller->dohvatiPostoveKorisnika();

if (isset($_GET['obrisi'])) {
    $obrisi_id = $_GET['obrisi'];
    $controller->obrisiPost($obrisi_id);
    header("Location: PostoviView.php");
    exit();
}
?>

<body>

    <div class="container" style="padding: 50px;">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#postovi" style="color: black">Svi
                    postovi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#moji" style="color: black">Moji</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane container active" id="postovi" style="padding: 20px">
                <?php foreach ($postovi as $post): ?>
                    <div class="card mb-3" style="width: 50%">
                        <div class="card-header">
                            <h4 style="text-align: left;">
                                <?php echo $post['naslov'] ?>
                            </h4>
                        </div>
                        <div class="card-body" style="text-align: left;">
                            <p>
                                <?php echo $post['sadrzaj'] ?>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <p style="text-align: left; margin-bottom: 0;">
                                <?php echo $post['datum_objave'] ?>
                            </p>
                            <a href="DetaljiPosta.php?post_id=<?php echo $post['post_id'] ?>"><i
                                    class="fa-regular fa-comments" style="font-size: x-large"></i></a>
                                    <?php if($_SESSION['vrsta_korisnika_id'] === 1){ ?>
                            <a href="#" class="btn btn-sm btn-outline-secondary"
                                onclick="obrisiPost(<?php echo $post['post_id']; ?>)"><i class="fa-solid fa-trash"></i></a>
                               <?php } ?>

                            </a>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <div class="tab-pane container fade" id="moji" style="padding: 20px">
            <?php if($mojiPostovi){ ?>
                <?php foreach ($mojiPostovi as $mojPost): ?>
                    <div class="card mb-3" style="width: 50%">
                        <div class="card-header">
                            <h4 style="text-align: left;">
                                <?php echo $mojPost['naslov'] ?>
                            </h4>
                        </div>
                        <div class="card-body" style="text-align: left;">
                            <p>
                                <?php echo $mojPost['sadrzaj'] ?>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <p style="text-align: left; margin-bottom: 0;">
                                <?php echo $mojPost['datum_objave'] ?>
                            </p>
                            <a href="DetaljiPosta.php?post_id=<?php echo $mojPost['post_id'] ?>"><i
                                    class="fa-regular fa-comments" style="font-size: x-large"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-secondary"
                                onclick="obrisiPost(<?php echo $mojPost['post_id']; ?>)"><i class="fa-solid fa-trash"></i></a>

                            </a>

                        </div>
                    </div>
                <?php endforeach; }else{
                    echo "Nemate još ni jedan post";
                } ?>
            </div>
        </div>
    </div>
</body>
<?php
?>

<script>
    function obrisiPost(postId) {
        var confirmed = confirm("Jeste li sigurni da želite obrisati ovaj post?");

        if (confirmed) {
            window.location.href = `PostoviView.php?obrisi=${postId}`;
        }
        header("Location: PostoviView.php");
        die();
    }

</script>