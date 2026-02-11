<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/Usuario.php";
require_once __DIR__ . "/../models/Profesional.php";

class MedicoController
{
    private $conn;
    private $usuarioModel;
    private $profesionalModel;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->usuarioModel = new Usuario($db);
        $this->profesionalModel = new Profesional($db);
    }

    public function listar()
    {
        $sql = "SELECT u.usuario_id, u.nombre_usuario, u.correo_electronico, 
                       u.nombre_completo, u.fecha_registro,
                       p.tarjeta_profesional, p.universidad,
                       STRING_AGG(e.descripcion, ', ') as especialidades
                FROM usuarios u
                LEFT JOIN profesionales p ON u.usuario_id = p.usuario_id
                LEFT JOIN profesionales_especialidades pe ON u.usuario_id = pe.usuario_id
                LEFT JOIN especialidades e ON pe.especialidad_id = e.especialidad_id
                WHERE u.rol_id = 3
                GROUP BY u.usuario_id, u.nombre_usuario, u.correo_electronico, 
                        u.nombre_completo, u.fecha_registro, p.tarjeta_profesional, p.universidad
                ORDER BY u.nombre_completo";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerEspecialidades()
    {
        return $this->profesionalModel->obtenerEspecialidades();
    }

    public function crear($data)
    {
        // Primero crear el usuario
        $usuario_data = [
            'nombre_usuario' => $data['nombre_usuario'],
            'correo_electronico' => $data['correo_electronico'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'rol_id' => 3, // Rol de Medico
            'nombre_completo' => $data['nombre_completo']
        ];

        $this->usuarioModel->crear($usuario_data);
        $usuario_id = $this->conn->lastInsertId();

        // Luego crear el profesional
        $profesional_data = [
            'usuario_id' => $usuario_id,
            'tarjeta_profesional' => $data['tarjeta_profesional'],
            'universidad' => $data['universidad']
        ];

        $this->profesionalModel->crear($profesional_data);

        // Asignar especialidades si se proporcionaron
        if (isset($data['especialidades']) && is_array($data['especialidades'])) {
            foreach ($data['especialidades'] as $especialidad_id) {
                $sql = "INSERT INTO profesionales_especialidades (usuario_id, especialidad_id) 
                        VALUES (:usuario_id, :especialidad_id)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':usuario_id' => $usuario_id,
                    ':especialidad_id' => $especialidad_id
                ]);
            }
        }

        return $usuario_id;
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT u.usuario_id, u.nombre_usuario, u.correo_electronico, 
                       u.nombre_completo, u.fecha_registro,
                       p.tarjeta_profesional, p.universidad
                FROM usuarios u
                LEFT JOIN profesionales p ON u.usuario_id = p.usuario_id
                WHERE u.usuario_id = :id AND u.rol_id = 3";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $data)
    {
        $sql = "UPDATE usuarios
                SET nombre_completo = :nombre_completo,
                    nombre_usuario = :nombre_usuario,
                    correo_electronico = :correo_electronico
                WHERE usuario_id = :id AND rol_id = 3";

        $stmt = $this->conn->prepare($sql);
        $okUsuario = $stmt->execute([
            ':nombre_completo' => $data['nombre_completo'],
            ':nombre_usuario' => $data['nombre_usuario'],
            ':correo_electronico' => $data['correo_electronico'],
            ':id' => $id
        ]);

        if (!$okUsuario) {
            return false;
        }

        if (!empty($data['password'])) {
            $sqlPass = "UPDATE usuarios SET password = :password WHERE usuario_id = :id AND rol_id = 3";
            $stmtPass = $this->conn->prepare($sqlPass);
            $okPass = $stmtPass->execute([
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':id' => $id
            ]);

            if (!$okPass) {
                return false;
            }
        }

        $profData = [
            'tarjeta_profesional' => $data['tarjeta_profesional'],
            'universidad' => $data['universidad']
        ];

        $okProfesional = $this->profesionalModel->actualizar($id, $profData);
        if (!$okProfesional) {
            return false;
        }

        return $this->profesionalModel->actualizarEspecialidades($id, $data['especialidades'] ?? []);
    }

    public function obtenerEspecialidadesMedico($medico_id)
    {
        $sql = "SELECT e.especialidad_id, e.descripcion
                FROM especialidades e
                INNER JOIN profesionales_especialidades pe ON e.especialidad_id = pe.especialidad_id
                WHERE pe.usuario_id = :medico_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':medico_id' => $medico_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($texto)
    {
        $sql = "SELECT u.usuario_id, u.nombre_usuario, u.correo_electronico, 
                       u.nombre_completo, u.fecha_registro,
                       p.tarjeta_profesional, p.universidad,
                       STRING_AGG(e.descripcion, ', ') as especialidades
                FROM usuarios u
                LEFT JOIN profesionales p ON u.usuario_id = p.usuario_id
                LEFT JOIN profesionales_especialidades pe ON u.usuario_id = pe.usuario_id
                LEFT JOIN especialidades e ON pe.especialidad_id = e.especialidad_id
                WHERE u.rol_id = 3 
                AND (u.nombre_completo ILIKE :texto OR u.correo_electronico ILIKE :texto)
                GROUP BY u.usuario_id, u.nombre_usuario, u.correo_electronico, 
                        u.nombre_completo, u.fecha_registro, p.tarjeta_profesional, p.universidad
                ORDER BY u.nombre_completo";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':texto', "%$texto%");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar($id)
    {
        // Eliminar especialidades del medico
        $sql = "DELETE FROM profesionales_especialidades WHERE usuario_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Eliminar profesional
        $sql = "DELETE FROM profesionales WHERE usuario_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Eliminar usuario
        return $this->usuarioModel->eliminar($id);
    }
}

// Manejo de acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireRole(['Administrador']);

    $db = new Database();
    $conn = $db->connect();
    $controller = new MedicoController($conn);

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'crear':
                $data = [
                    'nombre_usuario' => $_POST['nombre_usuario'],
                    'correo_electronico' => $_POST['correo_electronico'],
                    'password' => $_POST['password'],
                    'nombre_completo' => $_POST['nombre_completo'],
                    'tarjeta_profesional' => $_POST['tarjeta_profesional'],
                    'universidad' => $_POST['universidad'],
                    'especialidades' => $_POST['especialidades'] ?? []
                ];

                if ($controller->crear($data)) {
                    header("Location: ../views/medicos/listar.php?success=1");
                } else {
                    header("Location: ../views/medicos/crear.php?error=1");
                }
                exit;

            case 'actualizar':
                $id = $_POST['id'];
                $data = [
                    'nombre_completo' => $_POST['nombre_completo'],
                    'nombre_usuario' => $_POST['nombre_usuario'],
                    'correo_electronico' => $_POST['correo_electronico'],
                    'password' => $_POST['password'] ?? '',
                    'tarjeta_profesional' => $_POST['tarjeta_profesional'],
                    'universidad' => $_POST['universidad'],
                    'especialidades' => $_POST['especialidades'] ?? []
                ];

                if ($controller->actualizar($id, $data)) {
                    header("Location: ../views/medicos/listar.php?success=3");
                } else {
                    header("Location: ../views/medicos/editar.php?id=$id&error=1");
                }
                exit;

            case 'eliminar':
                $id = $_POST['id'];
                
                if ($controller->eliminar($id)) {
                    header("Location: ../views/medicos/listar.php?success=2");
                } else {
                    header("Location: ../views/medicos/listar.php?error=2");
                }
                exit;
        }
    }
}
