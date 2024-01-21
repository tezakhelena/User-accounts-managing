<?php

class PostoviController
{
    private $model;
    private $db;

    public function __construct()
    {
        $this->model = new PostoviModel();
    }

    public function dohvatiPostove()
    {
        $posts = $this->model->dohvatiPostove();
        if ($posts) {
            return $posts;
        } else {
            "Nema objava";
        }
    }

    public function dohvatiPostoveKorisnika()
    {
        $posts = $this->model->dohvatiPostoveKorisnika();
        if ($posts) {
            return $posts;
        } else {
            "Nema objava";
        }
    }

    public function dohvatiKomentare($postId)
    {
        $comments = $this->model->dohvatiKomentare($postId);
        return $comments;
    }

    public function dohvatiPost($postId)
    {
        $post = $this->model->dohvatiPost($postId);
        return $post;
    }

    public function dohvatiOdgovore($komentar_id)
    {
        $comments = $this->model->dohvatiOdgovore($komentar_id);
        return $comments;
    }

    public function prikaziKomentareIOdgovore($postId)
    {
        $komentari = $this->dohvatiKomentare($postId);
        $odgovori = [];

        foreach ($komentari as $komentar) {
            $odgovori[$komentar['komentar_id']] = $this->dohvatiOdgovore($komentar['komentar_id']);
        }

        if (isset($komentari, $odgovori)) {
            include '../view/PostoviView.php';
        } else {
            echo "Pogreška pri dohvaćanju podataka.";
        }

    }

    public function kreirajPost($korisnik_id, $naslov, $sadrzaj)
    {
        $this->model->kreirajPost($korisnik_id, $naslov, $sadrzaj);
        header("Location: /korisnik/view/PostoviView.php?korisnik_id=$korisnik_id");

    }

    public function urediPost($postId, $naslov, $sadrzaj)
    {
        $this->model->urediPost($postId, $naslov, $sadrzaj);
        header('Location: index.php');
    }

    public function obrisiPost($postId)
    {
        $this->model->obrisiKomentarNakonPosta($postId);
        $this->model->obrisiPost($postId);
        header('Location: /korisnik/view/PostoviView.php');
    }

    public function kreirajKomentar($postId, $sadrzaj)
    {
        $rezultat = $this->model->kreirajKomentar($postId, $sadrzaj);
        if ($rezultat) {
            header("Location: /korisnik/view/PostoviView.php");
        }
    }

    public function obrisiKomentar($komentar_id)
    {
        try {
            $this->model->obrisiOdgovorNakonKomentara($komentar_id);
            $this->model->obrisiKomentar($komentar_id);
            header("Location: /korisnik/view/DetaljiPosta.php?korisnik_id=$komentar_id");
        } catch (PDOException $e) {
            echo "Došlo je do greške prilikom komunikacije s bazom podataka: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Došlo je do neke druge greške: " . $e->getMessage();
        }
    }

    public function kreirajOdgovor($komentar_id, $sadrzaj)
    {
        $this->model->kreirajOdgovor($komentar_id, $sadrzaj);
    }

    public function obrisiOdgovor($replyId)
    {
        $this->model->obrisiOdgovor($replyId);
    }

}

?>