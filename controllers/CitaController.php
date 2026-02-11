<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/Cita.php";
require_once __DIR__ . "/../models/Paciente.php";
require_once __DIR__ . "/../models/Usuario.php";

class CitaController
{
    private $conn;
    private $model;
    private $pacienteModel;
    private $usuarioModel;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->model = new Cita($db);
        $this->pacienteModel = new Paciente($db);
        $this->usuarioModel = new Usuario($db);
    }

    public function index($fecha_inicio = null, $fecha_fin = null, $profesional_id = null)
    {
        return $this->model->listar($fecha_inicio, $fecha_fin, $profesional_id);
    }

    public function crear($data)
    {
        return $this->model->crear($data);
    }

    public function actualizar($cita_id, $data)
    {
        return $this->model->actualizar($cita_id, $data);
    }

    public function actualizarEstado($cita_id, $estado_id)
    {
        return $this->model->actualizarEstado($cita_id, $estado_id);
    }

    public function obtenerPorId($cita_id)
    {
        return $this->model->obtenerPorId($cita_id);
    }

    public function listarPorMedico($medico_id, $fecha_inicio = null, $fecha_fin = null)
    {
        return $this->model->listarPorMedico($medico_id, $fecha_inicio, $fecha_fin);
    }

    public function obtenerIndicadores($medico_id, $fecha_inicio, $fecha_fin)
    {
        return $this->model->obtenerIndicadores($medico_id, $fecha_inicio, $fecha_fin);
    }

    public function getPacientes()
    {
        return $this->pacienteModel->listar();
    }

    public function getMedicos()
    {
        $sql = "SELECT u.usuario_id, u.nombre_completo, e.descripcion as especialidad
                FROM usuarios u
                LEFT JOIN profesionales_especialidades pe ON u.usuario_id = pe.usuario_id
                LEFT JOIN especialidades e ON pe.especialidad_id = e.especialidad_id
                WHERE u.rol_id = 3
                ORDER BY u.nombre_completo";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEspecialidades()
    {
        $sql = "SELECT * FROM especialidades ORDER BY descripcion";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEstados()
    {
        return $this->model->obtenerEstados();
    }

    public function eliminar($cita_id)
    {
        return $this->model->eliminar($cita_id);
    }
}

// Manejo de acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin();

    $db = new Database();
    $conn = $db->connect();
    $controller = new CitaController($conn);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                requireRole(['Administrador', 'Recepcionista']);
                $data = [
                    'id_paciente' => $_POST['id_paciente'],
                    'documento_paciente' => $_POST['documento_paciente'],
                    'id_profesional' => $_POST['id_profesional'],
                    'especialidad_id' => $_POST['especialidad_id'],
                    'fecha_cita' => $_POST['fecha_cita'],
                    'hora_cita' => $_POST['hora_cita'],
                    'motivo' => $_POST['motivo']
                ];

                if ($controller->crear($data)) {
                    header("Location: ../views/citas/listar.php?success=1");
                } else {
                    header("Location: ../views/citas/crear.php?error=1");
                }
                exit;

            case 'actualizar':
                requireRole(['Administrador', 'Recepcionista']);
                $cita_id = $_POST['cita_id'];
                $data = [
                    'id_paciente' => $_POST['id_paciente'],
                    'documento_paciente' => $_POST['documento_paciente'],
                    'id_profesional' => $_POST['id_profesional'],
                    'especialidad_id' => $_POST['especialidad_id'],
                    'fecha_cita' => $_POST['fecha_cita'],
                    'hora_cita' => $_POST['hora_cita'],
                    'motivo' => $_POST['motivo']
                ];

                if ($controller->actualizar($cita_id, $data)) {
                    header("Location: ../views/citas/listar.php?success=4");
                } else {
                    header("Location: ../views/citas/editar.php?id=$cita_id&error=1");
                }
                exit;

            case 'actualizar_estado':
                requireRole(['Medico']);
                $cita_id = $_POST['cita_id'];
                $estado_id = $_POST['estado_id'];
                
                if ($controller->actualizarEstado($cita_id, $estado_id)) {
                    header("Location: ../views/citas/listar.php?success=2");
                } else {
                    header("Location: ../views/citas/listar.php?error=2");
                }
                exit;

            case 'eliminar':
                requireRole(['Administrador', 'Recepcionista']);
                $cita_id = $_POST['cita_id'];
                
                if ($controller->eliminar($cita_id)) {
                    header("Location: ../views/citas/listar.php?success=3");
                } else {
                    header("Location: ../views/citas/listar.php?error=3");
                }
                exit;
        }
    }
}
