<?php require_once APPROOT . '/views/layouts/header.php'; ?>
<div class="form-box" style="max-width:800px;">
    <h2>Editar Noticia</h2>
    <form action="<?= BASE_URL ?>noticia/editar/<?= $data['noticia']->id_noticia ?>" method="POST">
        <div class="form-group">
            <label>Título</label>
            <input type="text" name="titulo" value="<?= $data['noticia']->titulo ?>" required>
        </div>
        <div class="form-group">
            <label>Tipo</label>
            <select name="tipo">
                <option value="general" <?= $data['noticia']->tipo == 'general' ? 'selected' : '' ?>>General</option>
                <option value="convocatoria" <?= $data['noticia']->tipo == 'convocatoria' ? 'selected' : '' ?>>Convocatoria</option>
                <option value="semillero" <?= $data['noticia']->tipo == 'semillero' ? 'selected' : '' ?>>Semillero</option>
                <option value="voluntariado" <?= $data['noticia']->tipo == 'voluntariado' ? 'selected' : '' ?>>Voluntariado</option>
            </select>
        </div>
        <div class="form-group">
            <label>Contenido</label>
            <textarea name="contenido" required style="height:300px;"><?= $data['noticia']->contenido ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Noticia</button>
    </form>
</div>
<?php require_once APPROOT . '/views/layouts/footer.php'; ?>