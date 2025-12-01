<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<!-- ENCABEZADO Y BOTÓN DE ACCIÓN -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
    <div>
        <h2 style="color: var(--secondary);">Banco de Ideas</h2>
        <p style="color: #666; font-size: 0.9rem;">Explora, colabora y únete a proyectos innovadores.</p>
    </div>
    
    <!-- Lógica: Mostrar botón solo si el usuario ha iniciado sesión -->
    <?php if(isset($_SESSION['usuario_id'])): ?>
        <a href="<?= BASE_URL ?>idea/crear" class="btn btn-primary shadow">
            <span style="margin-right: 5px;">+</span> Publicar Nueva Idea
        </a>
    <?php else: ?>
        <div style="background: #e9ecef; padding: 10px 15px; border-radius: 5px; font-size: 0.85rem;">
            <a href="<?= BASE_URL ?>auth/login" style="color: var(--primary); font-weight: bold;">Inicia sesión</a> para proponer ideas.
        </div>
    <?php endif; ?>
</div>

<!-- SECCIÓN DE FILTROS -->
<div class="card" style="padding: 20px; background: #fff; border-left: 4px solid var(--secondary); margin-bottom: 30px;">
    <form action="<?= BASE_URL ?>idea/index" method="GET" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
        
        <div style="flex-grow: 1; min-width: 200px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Filtrar por Carrera:</label>
            <select name="carrera_id" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; background: white;">
                <option value="">-- Ver Todas las Carreras --</option>
                
                <?php foreach($data['carreras'] as $carrera): ?>
                    <option value="<?= $carrera->id_carrera ?>" 
                        <?php 
                            // Mantiene seleccionada la opción después de buscar
                            if($data['filtro_actual'] == $carrera->id_carrera) { echo 'selected'; } 
                        ?>
                    >
                        <?= $carrera->nombre ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 25px;">Buscar</button>
            
            <!-- Botón Limpiar: Solo aparece si hay un filtro activo -->
            <?php if($data['filtro_actual']): ?>
                <a href="<?= BASE_URL ?>idea/index" class="btn" style="background: #6c757d; color: white; height: 40px; line-height: 40px; display: inline-block; padding: 0 15px; margin-left: 5px;">
                    Limpiar Filtro
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- LISTADO DE IDEAS -->
<div class="lista-ideas">
    
    <!-- Mensaje si no hay resultados -->
    <?php if(empty($data['ideas'])): ?>
        <div style="text-align: center; padding: 50px; background: white; border-radius: 8px; border: 1px dashed #ccc;">
            <h3 style="color: #999;">No se encontraron ideas.</h3>
            <p style="color: #aaa;">Sé el primero en publicar una idea en esta categoría.</p>
        </div>
    <?php endif; ?>

    <!-- Bucle de Tarjetas -->
    <?php foreach($data['ideas'] as $idea): ?>
        
        <?php 
            // Lógica visual para los colores de las etiquetas según el estado
            $badgeColor = '#6c757d'; // Gris por defecto
            $badgeText = 'Desconocido';

            switch($idea->estado) {
                case 'abierta': 
                    $badgeColor = '#28a745'; // Verde
                    $badgeText = 'Abierta a Colaboración';
                    break;
                case 'en_desarrollo': 
                    $badgeColor = '#007bff'; // Azul
                    $badgeText = 'En Desarrollo';
                    break;
                case 'cerrada': 
                    $badgeColor = '#dc3545'; // Rojo
                    $badgeText = 'Culminada';
                    break;
            }
        ?>

        <div class="card" style="position: relative; transition: transform 0.2s;">
            
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                <h3 style="margin: 0; color: #333; font-size: 1.3rem;">
                    <?= $idea->titulo ?>
                </h3>
                <span class="badge" style="background: <?= $badgeColor ?>; color: white; font-weight: normal; font-size: 0.75rem;">
                    <?= $badgeText ?>
                </span>
            </div>

            <p style="color: #555; line-height: 1.6; margin-bottom: 20px;">
                <!-- Cortamos el texto a 200 caracteres para que no sea muy largo -->
                <?= substr($idea->descripcion, 0, 200) . (strlen($idea->descripcion) > 200 ? '...' : '') ?>
            </p>

            <div class="card-meta">
                <span>
                    👤 <strong><?= explode(' ', $idea->autor)[0] ?></strong> <!-- Solo primer nombre -->
                    <span style="color: #999; font-size: 0.8rem;">(<?= $idea->carrera ?? 'Sin carrera' ?>)</span>
                </span>
                <span style="color: #888; font-size: 0.85rem;">
                    📅 <?= date('d/m/Y', strtotime($idea->fecha_creacion)) ?>
                </span>
            </div>
            
            <div style="margin-top: 15px; text-align: right;">
                <a href="<?= BASE_URL ?>idea/detalle/<?= $idea->id_idea ?>" class="btn-outline">
                    Ver Detalles y Unirse &rarr;
                </a>
            </div>

        </div>
    <?php endforeach; ?>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>