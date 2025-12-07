<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Importante para móvil -->
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?= BASE_URL ?>" class="logo">PUIP - UNJBG</a>
            <ul class="nav-links">
                <!-- Accesos Públicos -->
                <li><a href="<?= BASE_URL ?>idea">Ideas</a></li>
                <li><a href="<?= BASE_URL ?>proyecto">Proyectos</a></li>
                <li><a href="<?= BASE_URL ?>foro">Foro</a></li>
                <li><a href="<?= BASE_URL ?>noticia">Noticias</a></li>

                <!-- Lógica de Sesión -->
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <?php 
                    // TRUCO: Consultar notificaciones directamente en la vista para el Header
                    // Solo si está logueado
                    $num_notificaciones = 0;
                    if(isset($_SESSION['usuario_id'])){
                        require_once '../app/models/Notificacion.php';
                        $notiTemp = new Notificacion();
                        $num_notificaciones = $notiTemp->contarNoLeidas($_SESSION['usuario_id']);
                    }
                ?>

                <!-- CAMPANA DE NOTIFICACIONES -->
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <li style="position: relative;">
                        <a href="<?= BASE_URL ?>perfil" style="font-size: 1.2rem;">🔔</a>
                        <?php if($num_notificaciones > 0): ?>
                            <span style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold;">
                                <?= $num_notificaciones ?>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>perfil">Mi Perfil</a></li>
                    <li><a href="<?= BASE_URL ?>auth/logout" class="btn-login">Salir</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>auth/login">Ingresar</a></li>
                    <li><a href="<?= BASE_URL ?>auth/registro" class="btn-login">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container" style="margin-top: 20px;">