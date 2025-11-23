drop database if exists plataforma_universitaria;
create database plataforma_universitaria character set utf8mb4 collate utf8mb4_unicode_ci;
use plataforma_universitaria;

/*
proposito: almacena las diferentes carreras universitarias.
esto evita la redundancia y errores de tipeo al registrar carreras,
permitiendo filtrar proyectos e ideas de manera consistente.
*/
create table carreras (
    id_carrera int primary key auto_increment,
    nombre varchar(150) not null unique,
    descripcion varchar(500)
);

/*
proposito: almacena la informacion de todos los usuarios registrados.
incluye un campo 'rol' para diferenciar entre 'estudiante' y 'administrador',
segun tus perfiles de usuario, la contraseña debe ser almacenada con hash. md5 sha1
*/
create table usuarios (
    id_usuario int primary key auto_increment,
    nombre_completo varchar(200) not null,
    correo_electronico varchar(255) not null unique,
    password_hash varchar(255) not null,
    carrera_id int,
    rol enum('estudiante', 'administrador') not null default 'estudiante',
    foto_perfil varchar(255),
    fecha_registro timestamp default current_timestamp,
    constraint fk_usuarios_carreras foreign key (carrera_id) references carreras(id_carrera) on delete set null
);

/*
proposito: almacena las ideas innovadoras propuestas por los estudiantes.
se vincula con el usuario que la creo.
*/
create table ideas (
    id_idea int primary key auto_increment,
    titulo varchar(255) not null,
    descripcion varchar(500) not null,
    estado enum('abierta', 'en_desarrollo', 'cerrada') not null default 'abierta',
    usuario_creador_id int not null,
    fecha_creacion timestamp default current_timestamp,
    constraint fk_ideas_usuarios foreign key (usuario_creador_id) references usuarios(id_usuario) on delete cascade
);

/*
proposito: tabla pivote para gestionar los miembros de un grupo de trabajo de una idea.
resuelve la relacion muchos-a-muchos entre usuarios e ideas.
*/
create table idea_miembros (
    idea_id int not null,
    usuario_id int not null,
    fecha_union timestamp default current_timestamp,
    primary key (idea_id, usuario_id),
    constraint fk_idea_miembros_ideas foreign key (idea_id) references ideas(id_idea) on delete cascade,
    constraint fk_idea_miembros_usuarios foreign key (usuario_id) references usuarios(id_usuario) on delete cascade
);

/*
proposito: almacena los proyectos interdisciplinarios que nacen de las ideas o se crean directamente.
tiene un estado para saber si esta 'vigente' o 'culminado'.
*/
create table proyectos (
    id_proyecto int primary key auto_increment,
    nombre varchar(255) not null,
    descripcion varchar(500) not null,
    estado enum('vigente', 'culminado') not null default 'vigente',
    usuario_creador_id int not null,
    fecha_inicio date,
    fecha_fin date,
    fecha_creacion timestamp default current_timestamp,
    idea_origen_id int unique, -- opcional, si el proyecto nace de una idea
    constraint fk_proyectos_usuarios foreign key (usuario_creador_id) references usuarios(id_usuario) on delete cascade,
    constraint fk_proyectos_ideas foreign key (idea_origen_id) references ideas(id_idea) on delete set null
);

/*
proposito: tabla pivote para asociar un proyecto con una o varias carreras.
esto permite la interdisciplinariedad.
*/
create table proyecto_carreras (
    proyecto_id int not null,
    carrera_id int not null,
    primary key (proyecto_id, carrera_id),
    constraint fk_proyecto_carreras_proyectos foreign key (proyecto_id) references proyectos(id_proyecto) on delete cascade,
    constraint fk_proyecto_carreras_carreras foreign key (carrera_id) references carreras(id_carrera) on delete cascade
);

/*
proposito: tabla pivote para que los usuarios puedan seguir proyectos y recibir notificaciones.
*/
create table proyecto_seguidores (
    proyecto_id int not null,
    usuario_id int not null,
    fecha_seguimiento timestamp default current_timestamp,
    primary key (proyecto_id, usuario_id),
    constraint fk_proyecto_seguidores_proyectos foreign key (proyecto_id) references proyectos(id_proyecto) on delete cascade,
    constraint fk_proyecto_seguidores_usuarios foreign key (usuario_id) references usuarios(id_usuario) on delete cascade
);

/*
proposito: almacena las categorias para las preguntas del foro (matematicas, programacion, etc.).
*/
create table foro_categorias (
    id_categoria int primary key auto_increment,
    nombre varchar(100) not null unique
);

/*
proposito: almacena las preguntas o temas creados en el foro de ayuda estudiantil.
*/
create table foro_publicaciones (
    id_foro_publicacion int primary key auto_increment,
    titulo varchar(255) not null,
    contenido varchar(1000) not null,
    usuario_id int not null,
    categoria_id int,
    fecha_publicacion timestamp default current_timestamp,
    constraint fk_foro_publicaciones_usuarios foreign key (usuario_id) references usuarios(id_usuario) on delete cascade,
    constraint fk_foro_publicaciones_categorias foreign key (categoria_id) references foro_categorias(id_categoria) on delete set null
);

/*
proposito: almacena las respuestas a las publicaciones del foro.
*/
create table foro_respuestas (
    id_foro_respuesta int primary key auto_increment,
    contenido varchar(1000) not null,
    publicacion_id int not null,
    usuario_id int not null,
    fecha_respuesta timestamp default current_timestamp,
    constraint fk_foro_respuestas_publicaciones foreign key (publicacion_id) references foro_publicaciones(id_foro_publicacion) on delete cascade,
    constraint fk_foro_respuestas_usuarios foreign key (usuario_id) references usuarios(id_usuario) on delete cascade
);

/*
proposito: almacena las noticias, convocatorias, semilleros y voluntariado.
*/
create table noticias (
    id_noticia int primary key auto_increment,
    titulo varchar(255) not null,
    contenido varchar(1000) not null,
    tipo enum('semillero', 'voluntariado', 'convocatoria', 'general') not null,
    usuario_publicador_id int not null,
    fecha_publicacion timestamp default current_timestamp,
    constraint fk_noticias_usuarios foreign key (usuario_publicador_id) references usuarios(id_usuario) on delete cascade
);

/*
proposito: almacena notificaciones para los usuarios (ej: nuevo seguidor, actualizacion de proyecto).
*/
create table notificaciones (
    id_notificacion int primary key auto_increment,
    usuario_id int not null,
    mensaje varchar(255) not null,
    leida boolean default false,
    enlace varchar(255), -- url a la que redirige la notificacion
    fecha_creacion timestamp default current_timestamp,
    constraint fk_notificaciones_usuarios foreign key (usuario_id) references usuarios(id_usuario) on delete cascade
);


INSERT INTO carreras (nombre) VALUES
('Ingenieria de Sistemas e Informatica'),
('Derecho y Ciencias Politicas'),
('Medicina Humana'),
('Arquitectura'),
('Ciencias de la Comunicacion'),
('Ingenieria Civil'),
('Contabilidad y Finanzas');


INSERT INTO usuarios (nombre_completo, correo_electronico, password_hash, carrera_id, rol, foto_perfil) VALUES
('Alberto Barrios Rivera', 'abarriosriv@unjbg.edu.pe', '12345', 1, 'estudiante', 'default.jpg'),
('Breyan Huisa Condori', 'huisa@unjbg.edu.pe', '12345', 1, 'estudiante', 'default.jpg'),
('Krishna Chiclaya Centeno', 'chiclaya@unjbg.edu.pe', '12345', 1, 'estudiante', 'default.jpg'),
('Alberto', 'barriosriv@unjbg.edu.pe', '12345', 1, 'administrador', 'default.jpg');


INSERT INTO ideas (titulo, descripcion, estado, usuario_creador_id) VALUES
('App movil para la reserva de laboratorios en la UNJBG', 'Una aplicacion para que los estudiantes puedan ver la disponibilidad y reservar horas en los laboratorios de computo.', 'abierta', 2),
('Red de voluntariado para apoyo a adultos mayores en Tacna', 'Crear una red de estudiantes voluntarios para realizar compania y compras para adultos mayores que viven solos.', 'abierta', 3),
('Plataforma para compartir apuntes y examenes pasados', 'Un sitio web donde los alumnos de todas las carreras puedan subir y descargar material de estudio de forma gratuita.', 'en_desarrollo', 1);


INSERT INTO idea_miembros (idea_id, usuario_id) VALUES
(1, 1),
(2, 2),
(3, 3);


INSERT INTO proyectos (nombre, descripcion, estado, usuario_creador_id, fecha_inicio, fecha_fin, idea_origen_id) VALUES
('Sistema de Monitoreo Ambiental para Tacna', 'Desarrollo de una red de sensores IoT para medir la calidad del aire en puntos clave de la ciudad de Tacna y visualizar los datos en un mapa web.', 'vigente', 2, '2022-07-01', NULL, NULL);


INSERT INTO proyecto_carreras (proyecto_id, carrera_id) VALUES
(1, 1),
(1, 7);


INSERT INTO proyecto_seguidores (proyecto_id, usuario_id) VALUES
(1, 1),
(1, 3);


INSERT INTO foro_categorias (nombre) VALUES
('Calculo y Matematicas'),
('Programacion Basica'),
('Derecho General'),
('Metodologia de Investigacion'),
('Fisica'),
('Ingenieria Web');


INSERT INTO foro_publicaciones (titulo, contenido, usuario_id, categoria_id) VALUES
('Ayuda con integral doble', 'Hola, alguien sabe cual es el integral de 2cosx*seny', 2, 1),
('Solucion de ejercicio', 'Hola, alguien sabe como se resulve este ejercicio de MRU ........', 3, 5);


INSERT INTO foro_respuestas (contenido, publicacion_id, usuario_id) VALUES
('Te recomiendo el canal de JulioProfe en YouTube, tiene videos muy claros sobre ese tema. Busca "integrales dobles JulioProfe" y te saldra.', 1, 1),
('Revisa un video xd', 2, 1);


INSERT INTO noticias (titulo, contenido, tipo, usuario_publicador_id) VALUES
('UNJBG organiza seminario sobre el uso de TikTok en la educación superior universitaria ', 
'La UNJBG desarrolló este lunes 29 de septiembre el Seminario “Uso del TikTok en la Educación Superior Universitaria', 'general', 4);
