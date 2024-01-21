<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

class KorisniciModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new PDO("pgsql:host=localhost;dbname=korisnicka_aplikacija", "postgres", "lozinka");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function generirajKorisnickoIme($ime, $prezime)
    {
        $query = "SELECT generiraj_korisnicko_ime(:ime, :prezime) AS korisnicko_ime";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':ime', $ime, PDO::PARAM_STR);
        $stmt->bindParam(':prezime', $prezime, PDO::PARAM_STR);
        $stmt->execute();

        $rezultat = $stmt->fetch(PDO::FETCH_ASSOC);
        return $rezultat['korisnicko_ime'];
    }

    public function registrirajKorisnika($ime, $prezime, $lozinka, $email, $omiljeniHobi, $vrsta_korisnika_id)
    {
        try {
            $korisnickoIme = $this->generirajKorisnickoIme($ime, $prezime);
            $hashLozinke = password_hash($lozinka, PASSWORD_DEFAULT);
            if (empty($email) || empty($lozinka)) {
                throw new PDOException("Unesite sva polja");
            } else {
                $query = "INSERT INTO korisnici (ime, prezime, korisnicko_ime, lozinka, email, vrsta_korisnika_id, omiljeni_hobi) VALUES (:ime, :prezime, :korisnicko_ime, :lozinka, :email, :vrsta_korisnika_id, :omiljeni_hobi)";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':ime', $ime);
                $stmt->bindParam(':prezime', $prezime);
                $stmt->bindParam(':korisnicko_ime', $korisnickoIme);
                $stmt->bindParam(':lozinka', $hashLozinke);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':vrsta_korisnika_id', $vrsta_korisnika_id);

                $omiljeniHobiArray = "{" . implode(",", array_map(function ($item) {
                    return '"' . $item . '"';
                }, $omiljeniHobi)) . "}";

                $stmt->bindValue(':omiljeni_hobi', $omiljeniHobiArray, PDO::PARAM_STR);
                $stmt->execute();

                return true;
            }
        } catch (PDOException $e) {
            echo 'Greška prilikom spremanja korisnika: ' . $e->getMessage();
            return false;
        } catch (Exception $e) {
            echo 'Greška';
        }
    }

    public function provjeriPrijavu($email, $lozinka)
    {
        $query = "SELECT*FROM korisnici WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $korisnik = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($korisnik && password_verify($lozinka, $korisnik['lozinka'])) {
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $korisnik['id'];
            $_SESSION['aktivan'] = $korisnik['aktivan'];
            $_SESSION['vrsta_korisnika_id'] = $korisnik['vrsta_korisnika_id'];
            $_SESSION['ime'] = $korisnik['ime'];
            return $korisnik['id'];
        } else {
            return false;
        }
    }

    public function dohvatiSveKorisnike()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM korisnici WHERE vrsta_korisnika_id <> 1");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dohvatiDetaljeKorisnika($id)
    {
        $query = "SELECT id, ime, prezime, korisnicko_ime, email, pretvori_u_samo_datum(datum_registracije::timestamp with time zone) AS datum_registracije, prikazi_hobije(omiljeni_hobi) AS omiljeni_hobi FROM korisnici WHERE id = :id;
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function azurirajKorisnika($id, $ime, $prezime, $korisnicko_ime, $email)
    {
        $query = "UPDATE korisnici SET ime = :ime, prezime = :prezime, korisnicko_ime = :korisnicko_ime, email = :email WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ime', $ime);
        $stmt->bindParam(':prezime', $prezime);
        $stmt->bindParam(':korisnicko_ime', $korisnicko_ime);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    public function izbrisiKorisnika($id)
    {
        try {
            $queryKorisnici = "DELETE FROM korisnici WHERE id = :id";
            $stmtKorisnici = $this->pdo->prepare($queryKorisnici);
            $stmtKorisnici->bindParam(':id', $id);
            return $stmtKorisnici->execute();;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new Exception("Greška prilikom brisanja korisnika: " . $e->getMessage());
        }
    }

    public function dovrsetakProfila($korisnikID, $adresa, $brojTelefona, $rodjendan, $spol, $biografija)
    {
        try {
            $sql = "INSERT INTO profili_korisnika (korisnik_id, adresa, broj_telefona, rodjendan, spol, biografske_informacije) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(1, $korisnikID, PDO::PARAM_INT);
            $stmt->bindParam(2, $adresa, PDO::PARAM_STR);
            $stmt->bindParam(3, $brojTelefona, PDO::PARAM_STR);
            $stmt->bindParam(4, $rodjendan, PDO::PARAM_STR);
            $stmt->bindParam(5, $spol, PDO::PARAM_STR);
            $stmt->bindParam(6, $biografija, PDO::PARAM_STR);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            throw new Exception("Greška prilikom dodavanja dodatnih informacija u profil: " . $e->getMessage());
        }
    }


    public function dohvatiKorisnikId($email)
    {
        try {
            $sql = "SELECT id FROM korisnici WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['id'];
        } catch (PDOException $e) {
            throw new Exception("Greška prilikom dohvaćanja korisnik_id: " . $e->getMessage());
        }
    }
    public function dohvatiPodatkeProfila($korisnikID)
    {
        try {
            $sql = "SELECT adresa, broj_telefona, spol, biografske_informacije, rodjendan FROM profili_korisnika WHERE korisnik_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$korisnikID]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Greška prilikom dohvaćanja podataka profila: " . $e->getMessage());
        }
    }

    public function getVrstaKorisnikaId($korisnik_id)
    {
        $stmt = $this->pdo->prepare('SELECT vrsta_korisnika_id FROM korisnici WHERE id = :korisnik_id');
        $stmt->bindParam(':korisnik_id', $korisnik_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['vrsta_korisnika_id'];
    }

    public function dohvatiDobKorisnika($korisnikId)
    {
        $query = "SELECT dob_korisnika(rodjendan) AS dob FROM profili_korisnika WHERE korisnik_id = :korisnikId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnikId', $korisnikId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function azurirajAktivnostKorisnika($korisnikId, $aktivan)
    {
        $query = "UPDATE korisnici SET aktivan = :aktivan WHERE id = :korisnikId";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':aktivan', $aktivan, PDO::PARAM_BOOL);
        $stmt->bindParam(':korisnikId', $korisnikId, PDO::PARAM_INT);
        $stmt->execute();
    }


    public function obrisiPostKorisnik($korisnik_id)
    {
        $query = "DELETE FROM postovi WHERE korisnik_id = :korisnik_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':korisnik_id', $korisnik_id, PDO::PARAM_INT);
        $stmt->execute();
    }

}
?>