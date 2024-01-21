<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

class PostoviModel
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO("pgsql:host=localhost;dbname=korisnicka_aplikacija", "postgres", "lozinka");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    public function dohvatiPostove()
    {
        $query = "SELECT postovi.*, korisnici.korisnicko_ime FROM postovi INNER JOIN korisnici ON postovi.korisnik_id = korisnici.id";
        $result = $this->pdo->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dohvatiPostoveKorisnika()
    {
        $korisnikId = $_SESSION['id'];
        $query = "SELECT postovi.*, korisnici.korisnicko_ime FROM postovi INNER JOIN korisnici ON postovi.korisnik_id = korisnici.id WHERE postovi.korisnik_id = :korisnikId";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnikId', $korisnikId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dohvatiKomentare($postId)
    {
        $query = "SELECT komentari.*, korisnici.korisnicko_ime FROM komentari INNER JOIN korisnici ON komentari.korisnik_id = korisnici.id WHERE komentari.post_id = :postId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dohvatiPost($postId)
    {
        $query = "SELECT postovi.naslov, postovi.sadrzaj, postovi.datum_objave, korisnici.korisnicko_ime FROM postovi INNER JOIN korisnici ON postovi.korisnik_id = korisnici.id WHERE postovi.post_id = :postId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function dohvatiOdgovore($komentarId)
    {
        $query = "SELECT odgovori.*, korisnici.korisnicko_ime FROM odgovori INNER JOIN korisnici ON odgovori.korisnik_id = korisnici.id WHERE odgovori.komentar_id = :komentarId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':komentarId', $komentarId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function kreirajPost($korisnik_id, $naslov, $sadrzaj)
    {
        $query = "INSERT INTO postovi (korisnik_id, naslov, sadrzaj) VALUES (:korisnik_id, :naslov, :sadrzaj)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnik_id', $korisnik_id, PDO::PARAM_INT);
        $stmt->bindParam(':naslov', $naslov, PDO::PARAM_STR);
        $stmt->bindParam(':sadrzaj', $sadrzaj, PDO::PARAM_STR);
        $stmt->execute();
    }


    public function urediPost($postId, $title, $sadrzaj)
    {
        $query = "UPDATE postovi SET naslov = :title, sadrzaj = :sadrzaj WHERE id = :postId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':sadrzaj', $sadrzaj, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function obrisiPost($postId)
    {
        $query = "DELETE FROM postovi WHERE post_id = :postId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function obrisiPostKorisnik($korisnik_id)
    {
        $query = "DELETE FROM postovi WHERE korisnik_id = :korisnik_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnik_id', $korisnik_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function kreirajKomentar($postId, $sadrzaj)
    {
        try {
            $korisnik_id = $_SESSION['id'];
            $query = "INSERT INTO komentari (post_id, korisnik_id, sadrzaj) VALUES (:postId, :korisnik_id, :sadrzaj)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':korisnik_id', $korisnik_id, PDO::PARAM_INT);
            $stmt->bindParam(':sadrzaj', $sadrzaj, PDO::PARAM_STR);
            $stmt->execute();

        } catch (PDOException $e) {
            echo 'Greška prilikom spremanja komentara: ' . $e->getMessage();
            return false;
        } catch (Exception $e) {
            echo 'Greška';
        }
    }

    public function urediKomentar($komentar_id, $sadrzaj)
    {
        $query = "UPDATE komentari SET sadrzaj = :sadrzaj WHERE id = :komentar_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':komentar_id', $komentar_id, PDO::PARAM_INT);
        $stmt->bindParam(':sadrzaj', $sadrzaj, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function obrisiKomentar($komentar_id)
    {
        $query = "DELETE FROM komentari WHERE komentar_id = :komentar_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':komentar_id', $komentar_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function kreirajOdgovor($komentar_id, $sadrzaj)
    {
        $korisnik_id = $_SESSION['id'];
        $query = "INSERT INTO odgovori (komentar_id, korisnik_id, sadrzaj) VALUES (:komentar_id, :korisnik_id, :sadrzaj)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':komentar_id', $komentar_id, PDO::PARAM_INT);
        $stmt->bindParam(':korisnik_id', $korisnik_id, PDO::PARAM_INT);
        $stmt->bindParam(':sadrzaj', $sadrzaj, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function urediOdgovor($odgovor_id, $sadrzaj)
    {
        $query = "UPDATE odgovori_komentara SET sadrzaj = :sadrzaj WHERE id = :odgovor_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':odgovor_id', $odgovor_id, PDO::PARAM_INT);
        $stmt->bindParam(':sadrzaj', $sadrzaj, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function obrisiOdgovor($odgovor_id)
    {
        $query = "DELETE FROM odgovori WHERE odgovor_id = :odgovor_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':odgovor_id', $odgovor_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function obrisiOdgovorNakonKomentara($komentar_id)
    {
        $query = "DELETE FROM odgovori WHERE komentar_id = :komentar_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':komentar_id', $komentar_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function obrisiKomentarNakonPosta($post_id)
    {
        try {
            if (!is_numeric($post_id)) {
                throw new Exception('Post ID mora biti broj.');
            }

            $query = "DELETE FROM komentari WHERE post_id = :post_id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            echo "Došlo je do greške: " . $e->getMessage();
        }
    }

}
?>