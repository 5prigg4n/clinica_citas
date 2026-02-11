<?php

class Cita
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listar($fecha_inicio = null, $fecha_fin = null, $profesional_id = null)
    {
        $sql = "SELECT c.*, 
                       p.primer_nombre || ' ' || p.primer_apellido AS paciente_nombre,
                       pr.nombre_completo AS profesional_nombre,
                       e.descripcion AS especialidad_desc,
                       ec.descripcion AS estado_desc
                FROM citas c
                LEFT JOIN pacientes p ON c.id_paciente = p.tipo_documento AND c.documento_paciente = p.numero_documento
                LEFT JOIN usuarios pr ON c.id_profesional::integer = pr.usuario_id
                LEFT JOIN especialidades e ON c.especialidad_id = e.especialidad_id
                LEFT JOIN estados_citas ec ON c.sw_estado = ec.id
                WHERE 1=1";

        $params = [];

        if ($fecha_inicio) {
            $sql .= " AND c.fecha_cita >= :fecha_inicio";
            $params[':fecha_inicio'] = $fecha_inicio;
        }

        if ($fecha_fin) {
            $sql .= " AND c.fecha_cita <= :fecha_fin";
            $params[':fecha_fin'] = $fecha_fin;
        }

        if ($profesional_id) {
            $sql .= " AND c.id_profesional::integer = :profesional_id";
            $params[':profesional_id'] = $profesional_id;
        }

        $sql .= " ORDER BY c.fecha_cita ASC, c.hora_cita ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorMedico($medico_id, $fecha_inicio = null, $fecha_fin = null)
    {
        return $this->listar($fecha_inicio, $fecha_fin, $medico_id);
    }

    public function crear($data)
    {
        // Validar que no exista una cita para el mismo medico en el mismo horario
        $sql_check = "SELECT COUNT(*) as count FROM citas 
                      WHERE id_profesional = :profesional_id 
                      AND fecha_cita = :fecha_cita 
                      AND hora_cita = :hora_cita 
                      AND sw_estado != 3"; 

        $stmt_check = $this->conn->prepare($sql_check);
        $stmt_check->execute([
            ':profesional_id' => $data['id_profesional'],
            ':fecha_cita' => $data['fecha_cita'],
            ':hora_cita' => $data['hora_cita']
        ]);

        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return false; // Ya existe una cita en ese horario
        }

        // Validar que la fecha no sea pasada
        if (strtotime($data['fecha_cita']) < strtotime(date('Y-m-d'))) {
            return false; 
        }

        $sql = "INSERT INTO citas 
                (id_paciente, documento_paciente, id_profesional, especialidad_id, 
                 fecha_cita, hora_cita, motivo, sw_estado)
                VALUES 
                (:id_paciente, :documento_paciente, :id_profesional, :especialidad_id,
                 :fecha_cita, :hora_cita, :motivo, 1)"; // 1 = Programada

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id_paciente' => $data['id_paciente'],
            ':documento_paciente' => $data['documento_paciente'],
            ':id_profesional' => $data['id_profesional'],
            ':especialidad_id' => $data['especialidad_id'],
            ':fecha_cita' => $data['fecha_cita'],
            ':hora_cita' => $data['hora_cita'],
            ':motivo' => $data['motivo']
        ]);
    }

    public function actualizar($cita_id, $data)
    {
        // Validar que no exista una cita para el mismo medico en el mismo horario
        $sql_check = "SELECT COUNT(*) as count FROM citas 
                      WHERE id_profesional = :profesional_id 
                      AND fecha_cita = :fecha_cita 
                      AND hora_cita = :hora_cita 
                      AND sw_estado != 3
                      AND cita_id != :cita_id";

        $stmt_check = $this->conn->prepare($sql_check);
        $stmt_check->execute([
            ':profesional_id' => $data['id_profesional'],
            ':fecha_cita' => $data['fecha_cita'],
            ':hora_cita' => $data['hora_cita'],
            ':cita_id' => $cita_id
        ]);

        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return false;
        }

        // Validar que la fecha no sea pasada
        if (strtotime($data['fecha_cita']) < strtotime(date('Y-m-d'))) {
            return false;
        }

        $sql = "UPDATE citas
                SET id_paciente = :id_paciente,
                    documento_paciente = :documento_paciente,
                    id_profesional = :id_profesional,
                    especialidad_id = :especialidad_id,
                    fecha_cita = :fecha_cita,
                    hora_cita = :hora_cita,
                    motivo = :motivo
                WHERE cita_id = :cita_id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id_paciente' => $data['id_paciente'],
            ':documento_paciente' => $data['documento_paciente'],
            ':id_profesional' => $data['id_profesional'],
            ':especialidad_id' => $data['especialidad_id'],
            ':fecha_cita' => $data['fecha_cita'],
            ':hora_cita' => $data['hora_cita'],
            ':motivo' => $data['motivo'],
            ':cita_id' => $cita_id
        ]);
    }

    public function actualizarEstado($cita_id, $estado_id)
    {
        $sql = "UPDATE citas SET sw_estado = :estado_id WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':estado_id' => $estado_id,
            ':cita_id' => $cita_id
        ]);
    }

    public function obtenerPorId($cita_id)
    {
        $sql = "SELECT c.*, 
                       p.primer_nombre || ' ' || p.primer_apellido AS paciente_nombre,
                       pr.nombre_completo AS profesional_nombre,
                       e.descripcion AS especialidad_desc,
                       ec.descripcion AS estado_desc
                FROM citas c
                LEFT JOIN pacientes p ON c.id_paciente = p.tipo_documento AND c.documento_paciente = p.numero_documento
                LEFT JOIN usuarios pr ON c.id_profesional::integer = pr.usuario_id
                LEFT JOIN especialidades e ON c.especialidad_id = e.especialidad_id
                LEFT JOIN estados_citas ec ON c.sw_estado = ec.id
                WHERE c.cita_id = :cita_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':cita_id' => $cita_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerEstados()
    {
        $sql = "SELECT * FROM estados_citas ORDER BY id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerIndicadores($medico_id, $fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT 
                    COUNT(*) as total_programadas,
                    SUM(CASE WHEN sw_estado = 2 THEN 1 ELSE 0 END) as total_atendidas,
                    SUM(CASE WHEN sw_estado = 3 THEN 1 ELSE 0 END) as total_no_asistidas
                FROM citas 
                WHERE id_profesional = :medico_id 
                AND fecha_cita BETWEEN :fecha_inicio AND :fecha_fin";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':medico_id' => $medico_id,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calcular porcentaje de cumplimiento
        $result['porcentaje_cumplimiento'] = $result['total_programadas'] > 0 
            ? round(($result['total_atendidas'] / $result['total_programadas']) * 100, 2)
            : 0;

        return $result;
    }

    public function eliminar($cita_id)
    {
        $sql = "DELETE FROM citas WHERE cita_id = :cita_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':cita_id' => $cita_id]);
    }
}
