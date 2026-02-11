<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <h2>Login</h2>

    <fieldset>
        <legend>Credenciales</legend>

        <form method='POST' action="../controllers/AuthController.php">
            <label>Usuario</label>
            <br>
            <input type="text" name="nombre_usuario" required>
            <br><br>

            <label>Contraseña</label>
            <br>
            <input type="password" name="password" required>
            <br><br>

            <button type="submit">Ingresar</button>
        </form>
    </fieldset>
    
</body>
</html>
