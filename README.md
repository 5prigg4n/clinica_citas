# Clinica Citas

Sistema de gestión de clínica 

## Requisitos

- PHP 7.x (XAMPP)
- Apache (XAMPP)
- PostgreSQL (la app prueba conexión y muestra `"Conexion a PostgreSQL exitosa"`)

## Instalación (Windows + XAMPP)

1. Clona o copia el proyecto dentro de:

   `C:\xampp\htdocs\clinica_citas`

2. Iniciar servicios:

- Apache (XAMPP)
- PostgreSQL (tu instalación local)

3. Configura la base de datos.

Este proyecto **requiere** el archivo `config/database.php` (está en `.gitignore`, por eso no se versiona). Si no lo tienes, créalo con una estructura similar a esta (ajusta host/puerto/usuario/clave/dbname):

```php
<?php

class Database {
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'clinica';
    private $user = 'postgres';
    private $password = 'postgres';

    public function connect() {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        $conn = new PDO($dsn, $this->user, $this->password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}
```

4. Verifica la conexión a DB (opcional):

- Abre en el navegador:

  `http://localhost/clinica_citas/public/index.php`

Si la conexión está bien, verás: `Conexion a PostgreSQL exitosa`.

## Uso

### Acceso

- Inicio:

  `http://localhost/clinica_citas/`

- Login:

  `http://localhost/clinica_citas/views/login.php`

El formulario envía a `controllers/AuthController.php`.

### Cerrar sesión

- `http://localhost/clinica_citas/views/logout.php`

### Módulos principales

Desde el `index.php` (cuando estás logueado) se puede entrar a:

- Usuarios: `views/usuarios/listar.php`
- Nuevo usuario: `views/usuarios/crear.php`
- Pacientes: `views/pacientes/listar.php`
- Médicos: `views/medicos/listar.php`
- Citas: `views/citas/listar.php`

## Notas técnicas

- La sesión se inicia en `config/session.php`.
- Para proteger páginas, existe `config/auth.php` con helpers `requireLogin()` y `requireRole([...])`.
- El login guarda en sesión `$_SESSION['usuario']` con:

  - `usuario_id`
  - `nombre_usuario`
  - `correo_electronico`
  - `rol`
  - `nombre_completo`



