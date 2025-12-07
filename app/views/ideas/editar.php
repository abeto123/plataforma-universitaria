<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="form-box">
    <h2 style="text-align:center; color:#f39c12;">Editar Idea</h2>
    <form action="<?= BASE_URL ?>idea/editar/<?= $data['idea']->id_idea ?>" method="POST">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" value="<?= $data['idea']->titulo ?>" required>
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="descripcion" required><?= $data['idea']->descripcion ?></textarea>
        </div>
        <div style="display:flex; justify-content:space-between;">
            <a href="<?= BASE_URL ?>idea/detalle/<?= $data['idea']->id_idea ?>" class="btn" style="background:#ddd;">Cancelar</a>
            <button type="submit" class="btn" style="background:#f39c12; color:white;">Guardar Cambios</button>
        </div>
    </form>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>