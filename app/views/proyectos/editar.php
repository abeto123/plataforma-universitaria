<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="form-box">
    <h2 style="color:#28a745;">Editar Proyecto</h2>
    <form action="<?= BASE_URL ?>proyecto/editar/<?= $data['proyecto']->id_proyecto ?>" method="POST">
        <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?= $data['proyecto']->nombre ?>" required>
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" required style="height:150px;"><?= $data['proyecto']->descripcion ?></textarea>
        </div>
        <button type="submit" class="btn" style="background:#28a745; color:white;">Guardar Cambios</button>
    </form>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>