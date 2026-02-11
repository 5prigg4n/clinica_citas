<?php
require_once '../models/Paciente.php';

class PacienteController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Paciente($db);
    }

    public function index()
    {
        return $this->model->listar();
    }

    public function buscar($texto)
    {
        return $this->model->buscar($texto);
    }

    public function guardar($data)
    {
        return $this->model->crear($data);
    }

    public function eliminar($tipo, $numero)
    {
        return $this->model->eliminar($tipo, $numero);
    }

    public function buscarPorDocumento($tipo, $numero)
{
    return $this->model->buscarPorDocumento($tipo, $numero);
}

}
