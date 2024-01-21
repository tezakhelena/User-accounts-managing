<?php

class NotifikacijeController
{
    private $model;
    private $db;

    public function __construct()
    {
        $this->model = new NotifikacijeModel();
    }

    public function dohvatiNotifikacije()
    {
        $notifikacije = $this->model->dohvatiNotifikacije();
        return $notifikacije;
    }

    public function dohvatiNeprocitaneNotifikacije()
    {
        $notifikacije = $this->model->dohvatiNeprocitaneNotifikacije();
        return $notifikacije;
    }

    public function oznaciSveNotifikacijeKaoProcitane()
    {
        $this->model->oznaciSveNotifikacijeKaoProcitane();
    }

}
?>