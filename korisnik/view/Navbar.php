<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="PocetnaView.php">Početna</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if($_SESSION['vrsta_korisnika_id'] === 1) {?>
                <li class="nav-item">
                    <a class="nav-link" href="KorisniciTable.php">Korisnici</a>
                </li>
                <?php }?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Postovi
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="PostoviView.php">Sve objave</a></li>
                        <?php if($_SESSION['aktivan'] == true){ ?>
                        <li><a class="dropdown-item" href="NoviPostView.php">Dodaj objavu</a></li>
                        <?php }?>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="navbar-nav ml-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <?php
                    echo $_SESSION['ime']; ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="NotifikacijeView.php">Notifikacije</a></li>
                    <li><a class="dropdown-item" href="Logout.php">Odjava</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>