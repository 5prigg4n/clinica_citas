<?php
class Profesional {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerEspecialidades() {
        $sql = "SELECT * FROM especialidades ORDER BY descripcion";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        $sql = "INSERT INTO profesionales
                (usuario_id, tarjeta_profesional, universidad)
                VALUES (:usuario_id, :tarjeta_profesional, :universidad)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function actualizar($usuario_id, $data)
    {
        $sql = "UPDATE profesionales
                SET tarjeta_profesional = :tarjeta_profesional,
                    universidad = :universidad
                WHERE usuario_id = :usuario_id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':tarjeta_profesional' => $data['tarjeta_profesional'],
            ':universidad' => $data['universidad'],
            ':usuario_id' => $usuario_id
        ]);
    }

    public function actualizarEspecialidades($usuario_id, $especialidades)
    {
        $sql = "DELETE FROM profesionales_especialidades WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);

        if (is_array($especialidades)) {
            foreach ($especialidades as $especialidad_id) {
                $sql = "INSERT INTO profesionales_especialidades (usuario_id, especialidad_id)
                        VALUES (:usuario_id, :especialidad_id)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':usuario_id' => $usuario_id,
                    ':especialidad_id' => $especialidad_id
                ]);
            }
        }

        return true;
    }

    public function obtenerPorUsuarioId($usuario_id) {
        $sql = "SELECT * FROM profesionales WHERE usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
