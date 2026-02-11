<?php

class Paciente
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function buscarPorDocumento($tipo, $numero)
{
    $sql = "SELECT * FROM pacientes
            WHERE tipo_documento = :tipo
            AND numero_documento = :numero";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipo,
        ':numero' => $numero
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function listarTiposDocumento()
{
    $sql = "SELECT * FROM tipos_documento ORDER BY descripcion";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function listarGeneros()
{
    $sql = "SELECT * FROM generos ORDER BY descripcion";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    
    public function listar()
    {
        $sql = "SELECT p.*, g.descripcion AS genero_descripcion
        FROM pacientes p
        LEFT JOIN generos g ON p.genero_id = g.genero_id
        ORDER BY primer_apellido ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function buscar($texto)
    {
        $sql = "SELECT * FROM pacientes 
                WHERE primer_nombre ILIKE :texto 
                OR numero_documento ILIKE :texto";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':texto', "%$texto%");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function crear($data)
    {
        $sql = "INSERT INTO pacientes 
                (tipo_documento, numero_documento, primer_nombre, segundo_nombre,
                 primer_apellido, segundo_apellido, genero_id, fecha_nacimiento,
                 telefono, correo, direccion)
                VALUES 
                (:tipo_documento, :numero_documento, :primer_nombre, :segundo_nombre,
                 :primer_apellido, :segundo_apellido, :genero_id, :fecha_nacimiento,
                 :telefono, :correo, :direccion)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function actualizar($data, $tipo_original, $numero_original)
    {
        $sql = "UPDATE pacientes 
                SET tipo_documento = :tipo_documento,
                    numero_documento = :numero_documento,
                    primer_nombre = :primer_nombre,
                    segundo_nombre = :segundo_nombre,
                    primer_apellido = :primer_apellido,
                    segundo_apellido = :segundo_apellido,
                    genero_id = :genero_id,
                    fecha_nacimiento = :fecha_nacimiento,
                    telefono = :telefono,
                    correo = :correo,
                    direccion = :direccion
                WHERE tipo_documento = :tipo_original 
                AND numero_documento = :numero_original";

        $stmt = $this->conn->prepare($sql);

        $params = array_merge($data, [
            ':tipo_original' => $tipo_original,
            ':numero_original' => $numero_original
        ]);

        return $stmt->execute($params);
    }

   
    public function eliminar($tipo_documento, $numero_documento)
    {
        $sql = "DELETE FROM pacientes 
                WHERE tipo_documento = :tipo_documento 
                AND numero_documento = :numero_documento";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':tipo_documento' => $tipo_documento,
            ':numero_documento' => $numero_documento
        ]);
    }
}
