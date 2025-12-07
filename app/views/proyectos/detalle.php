<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container" style="margin-top: 30px;">
    <a href="<?= BASE_URL ?>proyecto" style="color: #666;">&larr; Volver a Proyectos</a>

    <!-- CABECERA DE PROYECTO -->
    <div style="background: white; padding: 30px; border-radius: 8px; margin-top: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 5px solid #b30000;">
        
        <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap;">
            <div style="flex: 1;">
                <h1 style="margin-bottom: 10px;"><?= $data['proyecto']->nombre ?></h1>
                
                <!-- Carreras involucradas -->
                <div style="display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 15px;">
                    <?php foreach($data['carreras'] as $c): ?>
                        <span class="badge" style="background: #f5e8e8ff; color: #b30000; border: 1px solid #e6c8c8ff;">
                            <?= $c->nombre ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <p style="color: #555; font-size: 1.1rem; line-height: 1.6;">
                    <?= nl2br($data['proyecto']->descripcion) ?>
                </p>
            </div>

            <!-- BOTÓN SEGUIR -->
            <div style="text-align: center; margin-left: 20px; min-width: 150px;">
                <div style="font-size: 2rem; font-weight: bold; color: #b30000;">
                    <?= $data['seguidores'] ?>
                </div>
                <div style="color: #888; font-size: 0.8rem; margin-bottom: 10px;">SEGUIDORES</div>

                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <a href="<?= BASE_URL ?>proyecto/seguir/<?= $data['proyecto']->id_proyecto ?>" 
                       class="btn" 
                       style="background: <?= $data['siguiendo'] ? '#dc3545' : '#b30000' ?>; color: white; width: 100%;">
                        <?= $data['siguiendo'] ? 'Dejar de Seguir' : '🔔 Seguir' ?>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>auth/login" class="btn" style="background: #b30000; color: white; width: 100%;">Ingresa para Seguir</a>
                <?php endif; ?>
            </div>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
        
        <div style="color: #777;">
            <strong>Responsable:</strong> <?= $data['proyecto']->responsable ?><br>
            <strong>Inicio:</strong> <?= date('d/m/Y', strtotime($data['proyecto']->fecha_inicio)) ?>
        </div>
    </div>
    <!-- SOLO EL DUEÑO VE ESTO -->
    <?php if(isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $data['proyecto']->usuario_creador_id): ?>
        <hr>
        <div style="text-align: right;">
            <a href="<?= BASE_URL ?>proyecto/editar/<?= $data['proyecto']->id_proyecto ?>" style="margin-right:15px; color:#d35400;">✎ Editar</a>
            <a href="<?= BASE_URL ?>proyecto/eliminar/<?= $data['proyecto']->id_proyecto ?>" onclick="return confirm('¿Borrar proyecto?');" style="color:red;">🗑 Eliminar</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>