<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Proyectos Interdisciplinarios</h2>
    <?php if(isset($_SESSION['usuario_id'])): ?>
        <a href="<?= BASE_URL ?>proyecto/crear" class="btn" style="background: #b30000; color: white;">+ Nuevo Proyecto</a>
    <?php endif; ?>
</div>

<!-- FILTROS -->
<div class="card" style="padding: 15px; background: #f5e8e8ff; border: 1px solid #e6c8c8ff; border-left: none;">
    <form action="<?= BASE_URL ?>proyecto/index" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
        
        <select name="carrera" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="">-- Todas las Carreras --</option>
            <?php foreach($data['carreras'] as $c): ?>
                <option value="<?= $c->id_carrera ?>" <?= $data['f_carrera'] == $c->id_carrera ? 'selected' : '' ?>>
                    <?= $c->nombre ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="estado" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="">-- Todos los Estados --</option>
            <option value="vigente" <?= $data['f_estado'] == 'vigente' ? 'selected' : '' ?>>Vigentes</option>
            <option value="culminado" <?= $data['f_estado'] == 'culminado' ? 'selected' : '' ?>>Culminados</option>
        </select>

        <button type="submit" class="btn" style="background: #b30000; color: white; padding: 8px 15px;">Filtrar</button>
    </form>
</div>

<!-- LISTADO -->
<div class="dashboard-grid">
    <?php foreach($data['proyectos'] as $proy): ?>
        <div class="dashboard-card" style="border-top: 5px solid <?= $proy->estado == 'vigente' ? '#b30000' : '#6c757d' ?>;">
            <div>
                <span class="badge" style="float:right; background: <?= $proy->estado == 'vigente' ? '#edd4d4ff' : '#e5e2e2ff' ?>; color: <?= $proy->estado == 'vigente' ? '#b30000' : '#383d41' ?>;">
                    <?= ucfirst($proy->estado) ?>
                </span>
                <h3><?= $proy->nombre ?></h3>
                <p><?= substr($proy->descripcion, 0, 150) ?>...</p>
                <div style="font-size: 0.85rem; color: #666; margin-top: 10px;">
                    Responsable: <strong><?= explode(' ', $proy->responsable)[0] ?></strong>
                </div>
            </div>
            <a href="<?= BASE_URL ?>proyecto/detalle/<?= $proy->id_proyecto ?>" class="btn-outline" style="border-color: #b30000; color: #b30000; width:100%; margin-top:15px;">
                Ver Avances
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>