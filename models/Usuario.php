<?php
class Usuario {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listar() {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRoles() {
        $sql = "SELECT * FROM roles";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function crear($data) {

    $sql = "INSERT INTO usuarios
            (nombre_usuario,
             correo_electronico,
             password,
             rol_id,
             fecha_registro,
             nombre_completo)
            VALUES
            (:nombre_usuario,
             :correo_electronico,
             :password,
             :rol_id,
             NOW(),
             :nombre_completo)";

    $stmt = $this->conn->prepare($sql);

    return $stmt->execute($data);
}

    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE usuario_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
