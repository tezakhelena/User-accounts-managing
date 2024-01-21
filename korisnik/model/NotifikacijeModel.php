<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

class NotifikacijeModel
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO("pgsql:host=localhost;dbname=korisnicka_aplikacija", "postgres", "lozinka");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function oznaciSveNotifikacijeKaoProcitane()
    {
        $korisnikId = $_SESSION['id'];
        $query = "UPDATE notifikacije SET procitano = true WHERE korisnik_id = :korisnik_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnik_id', $korisnikId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function dohvatiNotifikacije()
    {
        $korisnik_id = $_SESSION['id'];

        $query = "SELECT * FROM notifikacije WHERE korisnik_id = :korisnik_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnik_id', $korisnik_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dohvatiNeprocitaneNotifikacije()
    {
        $korisnik_id = $_SESSION['id'];

        $query = "SELECT * FROM notifikacije WHERE korisnik_id = :korisnik_id AND procitano = false";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnik_id', $korisnik_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>