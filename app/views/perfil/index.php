<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<!-- ESTILOS PROPIOS PARA EL PERFIL -->
<style>
    .perfil-header {
        background: linear-gradient(to right, #2c3e50, #4ca1af);
        color: white;
        padding: 40px;
        border-radius: 8px 8px 0 0;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .avatar-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .stat-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    .stat-number { font-size: 2rem; font-weight: bold; color: var(--primary); }
    .stat-label { color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
</style>

<div class="container" style="margin-top: 30px;">
    
    <!-- 1. CABECERA DEL PERFIL -->
    <div class="card" style="padding: 0; border: none; overflow: hidden;">
        <div class="perfil-header">
            <!-- Avatar automático con iniciales -->
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($data['usuario']->nombre_completo) ?>&background=random&size=128" 
                 alt="Avatar" class="avatar-circle">
            
            <div>
                <h1 style="margin: 0;"><?= $data['usuario']->nombre_completo ?></h1>
                <p style="margin: 5px 0; opacity: 0.9;"><?= $data['usuario']->correo_electronico ?></p>
                <span class="badge" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5);">
                    <?= $data['usuario']->nombre_carrera ?? 'Sin carrera asignada' ?>
                </span>
                <span class="badge" style="background: #ffc107; color: #333; margin-left: 5px;">
                    Rol: <?= ucfirst($data['usuario']->rol) ?>
                </span>
            </div>
        </div>

    </div>

    <!-- CENTRO DE NOTIFICACIONES -->
<div class="card" style="margin-top: 20px; border-left: 5px solid #ffc107;">
    <h3>🔔 Actividad Reciente</h3>
    
    <?php if(empty($data['notificaciones'])): ?>
        <p style="color: #999;">No tienes notificaciones nuevas.</p>
    <?php else: ?>
        <ul style="list-style: none;">
            <?php foreach($data['notificaciones'] as $noti): ?>
                
                <!-- Si no está leída, le ponemos fondo amarillito -->
                <li style="padding: 10px; border-bottom: 1px solid #eee; <?= $noti->leida == 0 ? 'background: #fffbf2; font-weight:bold;' : '' ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        
                        <!-- Mensaje -->
                        <span><?= $noti->mensaje ?></span>
                        
                        <!-- Botón para ver (Marca como leída) -->
                        <a href="<?= BASE_URL ?>perfil/leer/<?= $noti->id_notificacion ?>" class="btn" style="font-size: 0.8rem; padding: 5px 10px; border: 1px solid #ddd;">
                            Ver &rarr;
                        </a>

                    </div>
                    <small style="color: #999; font-weight: normal;"><?= date('d/m/Y H:i', strtotime($noti->fecha_creacion)) ?></small>
                </li>

            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

    <!-- 2. ESTADÍSTICAS (GRID) -->
    <div class="dashboard-grid" style="margin-top: -20px; position: relative; z-index: 10;">
        <div class="stat-box">
            <div class="stat-number"><?= $data['stats']->ideas ?></div>
            <div class="stat-label">Ideas Publicadas</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= $data['stats']->proyectos_seguidos ?></div>
            <div class="stat-label">Proyectos Seguidos</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= $data['stats']->preguntas ?></div>
            <div class="stat-label">Participaciones en Foro</div>
        </div>
    </div>

    <div style="display: flex; gap: 30px; margin-top: 30px; flex-wrap: wrap;">
        
        <!-- 3. COLUMNA IZQUIERDA: EDITAR DATOS -->
        <div style="flex: 1; min-width: 300px;">
            <div class="card">
                <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">✏ Editar Información</h3>
                
                <form action="<?= BASE_URL ?>perfil/actualizar" method="POST">
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" value="<?= $data['usuario']->nombre_completo ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Carrera Profesional</label>
                        <select name="carrera_id" required>
                            <?php foreach($data['carreras'] as $carrera): ?>
                                <option value="<?= $carrera->id_carrera ?>" <?= $data['usuario']->carrera_id == $carrera->id_carrera ? 'selected' : '' ?>>
                                    <?= $carrera->nombre ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- Dentro del formulario de editar perfil -->
                    <div class="form-group">
                        <label>Número de Celular (WhatsApp)</label>
                        <input type="text" name="telefono" value="<?= $data['usuario']->telefono ?>" placeholder="Ej: 952123456">
                        <small style="color: #888;">Solo será visible para el líder del equipo si te unes a una idea.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>

                <hr style="margin: 30px 0;">

                <h3 style="margin-bottom: 15px;">🔒 Seguridad</h3>
                <form action="<?= BASE_URL ?>perfil/cambiar_password" method="POST">
                    <div class="form-group">
                        <label>Nueva Contraseña</label>
                        <input type="password" name="password_nueva" placeholder="Mínimo 4 caracteres" required>
                    </div>
                    <button type="submit" class="btn" style="background: #666; color: white;">Actualizar Contraseña</button>
                </form>
            </div>
        </div>

        <!-- 4. COLUMNA DERECHA: ACCESOS RÁPIDOS -->
        <div style="flex: 1; min-width: 300px;">
            <div class="card">
                <h3>📂 Mis Accesos Directos</h3>
                <p style="color: #666; margin-bottom: 20px;">Gestiona tu contenido académico.</p>
                
                <a href="<?= BASE_URL ?>idea/crear" class="list-item" style="display: block; color: var(--primary);">
                    <h4>💡 Crear Nueva Idea</h4>
                    <p>Propón una solución innovadora.</p>
                </a>

                <a href="<?= BASE_URL ?>foro/crear" class="list-item" style="display: block; color: #007bff;">
                    <h4>❓ Hacer Pregunta en el Foro</h4>
                    <p>Solicita ayuda a la comunidad.</p>
                </a>

                <!-- Si tuviéramos una página de "Mis Ideas", el link iría aquí -->
                <div class="list-item">
                    <h4>📊 Mis Proyectos</h4>
                    <p>Actualmente participas en <strong><?= $data['stats']->proyectos_seguidos ?></strong> proyectos.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>