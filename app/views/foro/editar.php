<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="form-box">
    <h2>Editar Pregunta</h2>
    <form action="<?= BASE_URL ?>foro/editar/<?= $data['pregunta']->id_foro_publicacion ?>" method="POST">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" value="<?= $data['pregunta']->titulo ?>" required>
        </div>
        <div class="form-group">
            <label>Contenido</label>
            <textarea name="contenido" required style="height:150px;"><?= $data['pregunta']->contenido ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>