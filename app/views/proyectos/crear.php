<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="form-box" style="max-width: 700px;">
    <h2 style="text-align: center; color: #a72828ff;">Registrar Nuevo Proyecto</h2>
    
    <form action="<?= BASE_URL ?>proyecto/crear" method="POST">
        <div class="form-group">
            <label>Nombre del Proyecto</label>
            <input type="text" name="nombre" required>
        </div>
        
        <div class="form-group">
            <label>Descripción y Objetivos</label>
            <textarea name="descripcion" required style="height: 150px;"></textarea>
        </div>

        <div class="form-group">
            <label>Carreras Involucradas (Interdisciplinariedad)</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                <?php foreach($data['carreras'] as $carrera): ?>
                    <label style="font-weight: normal; font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="carreras[]" value="<?= $carrera->id_carrera ?>">
                        <?= $carrera->nombre ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <small style="color: #666;">Selecciona todas las carreras que participan.</small>
        </div>

        <button type="submit" class="btn btn-block" style="background: #a72828ff; color: white;">Crear Proyecto</button>
    </form>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>