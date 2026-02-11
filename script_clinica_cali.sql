-- =========================
-- TABLAS BASE
-- =========================

CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE tipos_documento (
    tipo_documento VARCHAR(3) PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL
);

CREATE TABLE generos (
    genero_id VARCHAR(3) PRIMARY KEY,
    descripcion VARCHAR(20)
);

CREATE TABLE estados_citas (
    id SERIAL PRIMARY KEY,
    descripcion VARCHAR(15)
);

CREATE TABLE especialidades (
    especialidad_id SERIAL PRIMARY KEY,
    descripcion VARCHAR(100)
);

-- =========================
-- USUARIOS
-- =========================

CREATE TABLE usuarios (
    usuario_id SERIAL PRIMARY KEY,
    nombre_usuario VARCHAR(8) NOT NULL,
    correo_electronico VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol_id INTEGER NOT NULL,
    fecha_registro TIMESTAMP WITH TIME ZONE NOT NULL,
    nombre_completo VARCHAR(40),
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- =========================
-- PACIENTES
-- =========================

CREATE TABLE pacientes (
    tipo_documento VARCHAR(3) NOT NULL,
    numero_documento VARCHAR(50) NOT NULL,
    primer_nombre VARCHAR(150),
    segundo_nombre VARCHAR(150),
    primer_apellido VARCHAR(150),
    segundo_apellido VARCHAR(150),
    genero_id VARCHAR(3),
    fecha_nacimiento DATE,
    telefono VARCHAR(30),
    correo VARCHAR(100),
    direccion TEXT,
    PRIMARY KEY (tipo_documento, numero_documento),
    FOREIGN KEY (tipo_documento) REFERENCES tipos_documento(tipo_documento),
    FOREIGN KEY (genero_id) REFERENCES generos(genero_id)
);

-- =========================
-- PROFESIONALES
-- =========================

CREATE TABLE profesionales (
    tipo_documento VARCHAR(3),
    numero_documento VARCHAR(50),
    usuario_id INTEGER,
    tarjeta_profesional VARCHAR(20),
    universidad VARCHAR(50),
    UNIQUE (tipo_documento, numero_documento),
    FOREIGN KEY (tipo_documento, numero_documento)
        REFERENCES pacientes(tipo_documento, numero_documento),
    FOREIGN KEY (usuario_id)
        REFERENCES usuarios(usuario_id)
);

CREATE TABLE profesionales_especialidades (
    usuario_id INTEGER,
    especialidad_id INTEGER,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id),
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(especialidad_id)
);

-- =========================
-- CITAS
-- =========================

CREATE TABLE citas (
    cita_id SERIAL PRIMARY KEY,
    id_paciente VARCHAR(3),
    documento_paciente VARCHAR(50),
    id_profesional VARCHAR(3),
    documento_profesional VARCHAR(50),
    especialidad_id INTEGER,
    fecha_cita DATE,
    hora_cita TIME,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    motivo TEXT,
    sw_estado INTEGER DEFAULT 1,
    FOREIGN KEY (id_paciente, documento_paciente)
        REFERENCES pacientes(tipo_documento, numero_documento),
    FOREIGN KEY (id_profesional, documento_profesional)
        REFERENCES profesionales(tipo_documento, numero_documento),
    FOREIGN KEY (especialidad_id)
        REFERENCES especialidades(especialidad_id),
    FOREIGN KEY (sw_estado)
        REFERENCES estados_citas(id)
);
