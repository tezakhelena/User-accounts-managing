<?php
class KorisniciController
{
    private $model;
    private $db;

    public function __construct()
    {
        $this->model = new KorisniciModel();
    }

    public function registracija($ime, $prezime, $lozinka, $email, $omiljeniHobi, $vrsta_korisnika_id)
    {
        try {
            $rezultat = $this->model->registrirajKorisnika($ime, $prezime, $lozinka, $email, $omiljeniHobi, $vrsta_korisnika_id);

            if ($rezultat) {
                $korisnik_id = $this->model->dohvatiKorisnikId($email);
                $_SESSION['temp_korisnik_id'] = $korisnik_id;
                $_SESSION['ime'] = $ime;
                $_SESSION['vrsta_korisnika_id'] = $vrsta_korisnika_id;
                header("Location: /korisnik/view/DovrsiProfil.php?korisnik_id=$korisnik_id");
            } else {
                echo "Greška prilikom registracije.";
            }
        } catch (PDOException $e) {
            echo "Došlo je do greške prilikom komunikacije s bazom podataka: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Došlo je do neke druge greške: " . $e->getMessage();
        }
    }

    public function prijava($email, $lozinka)
    {
        $korisnik = $this->model->provjeriPrijavu($email, $lozinka);

        if ($korisnik) {
            header("Location: /korisnik/view/PocetnaView.php");
            exit();
        } else {
            echo "Neuspješna prijava. Provjerite korisničko ime i lozinku.";
        }
    }

    public function prikaziSveKorisnike()
    {
        $korisnik_id = $_SESSION['id'];
        $vrsta_korisnika_id = $this->model->getVrstaKorisnikaId($korisnik_id);
        $admin_prava = $this->is_admin($vrsta_korisnika_id);

        if ($admin_prava) {
            $korisnici = $this->model->dohvatiSveKorisnike();
            if ($korisnici) {
                return $korisnici;
            } else {
                echo "Nema podataka za prikaz";
            }
        } else {
            echo "Nemate pravo pristupiti ovom ekranu";
        }
    }

    public function dohvatiDetalje($id)
    {
        $detalji = $this->model->dohvatiDetaljeKorisnika($id);
        return $detalji;

    }

    public function dohvatiProfilKorisnika($id)
    {
        $profil = $this->model->dohvatiPodatkeProfila($id);
        return $profil;
    }

    public function dohvatiDobKorisnika($id)
    {
        $dob = $this->model->dohvatiDobKorisnika($id);
        return $dob;
    }

    public function azurirajKorisnika($id, $ime, $prezime, $korisnicko_ime, $email)
    {
        $uspjeh = $this->model->azurirajKorisnika($id, $ime, $prezime, $korisnicko_ime, $email);

        if ($uspjeh) {
            header("Location: /korisnik/view/KorisniciTable.php");
        } else {
            echo "Greška pri ažuriranju.";
        }
    }

    public function izbrisiKorisnika($id)
    {
        $uspjeh = $this->model->izbrisiKorisnika($id);

        if ($uspjeh) {
            header("Location: /korisnik/view/KorisniciTable.php");
        } else {
            echo "Greška pri brisanju korisnika.";
        }
    }

    public function dovrsetakProfila($korisnikID, $adresa, $brojTelefona, $rodjendan, $spol, $biografija)
    {
        try {
            $rezultat = $this->model->dovrsetakProfila($korisnikID, $adresa, $brojTelefona, $rodjendan, $spol, $biografija);

            if ($rezultat) {
                $_SESSION['korisnik_prijavljen'] = true;
                $_SESSION['id'] = $korisnikID;
                header("Location: /korisnik/view/PocetnaView.php");
            } else {
                echo "Greška prilikom dovršetka profila.";
            }
        } catch (PDOException $e) {
            echo "Došlo je do greške prilikom komunikacije s bazom podataka: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Došlo je do neke druge greške: " . $e->getMessage();
        }
    }

    public function is_admin($vrsta_korisnika_id)
    {
        return $vrsta_korisnika_id == 1;
    }

    public function deaktivirajKorisnika($korisnikId)
    {
        try {
            $aktivan = false;
            $this->model->azurirajAktivnostKorisnika($korisnikId, $aktivan);
            header("Location: /korisnik/view/KorisniciTable.php");
            exit();
        } catch (PDOException $e) {
            echo "Došlo je do greške prilikom komunikacije s bazom podataka: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Došlo je do neke druge greške: " . $e->getMessage();
        }

    }

    public function obrisiPostKorisnik($korisnik_id)
    {
        $this->model->obrisiPostKorisnik($korisnik_id);
        header('Location: /korisnik/view/KorisniciTable.php');
    }

    public function aktivirajKorisnika($korisnikId)
    {
        try {
            $aktivan = true;
            $this->model->azurirajAktivnostKorisnika($korisnikId, $aktivan);
            header("Location: /korisnik/view/KorisniciTable.php");
            exit();
        } catch (PDOException $e) {
            echo "Došlo je do greške prilikom komunikacije s bazom podataka: " . $e->getMessage();
        } catch (Exception $e) {
            echo "Došlo je do neke druge greške: " . $e->getMessage();
        }

    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: /korisnik/index.php");
        exit();
    }

}
?>