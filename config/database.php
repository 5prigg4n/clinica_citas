<?php

class Database {

    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;

    public function __construct() {

        $this->host = 'localhost';
        $this->db_name = 'clinica_citas';
        $this->username = 'postgres';
        $this->password = 'postgres';
        $this->port = '5432';
    }

    public function connect() {
        try {

            $conn = new PDO(
                "pgsql:host=".$this->host.";port=".$this->port.";dbname=".$this->db_name,
                $this->username,
                $this->password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch (PDOException $e) {
            die("Error al conectarse: " . $e->getMessage());
        }
    }
}
