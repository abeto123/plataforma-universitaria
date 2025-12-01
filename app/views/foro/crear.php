<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="form-box">
    <h2 style="color:#007bff; text-align:center;">Nueva Pregunta</h2>
    
    <form action="<?= BASE_URL ?>foro/crear" method="POST">
        <div class="form-group">
            <label>Título de la pregunta</label>
            <input type="text" name="titulo" placeholder="Ej: ¿Cómo integrar PayPal en PHP?" required>
        </div>

        <div class="form-group">
            <label>Categoría</label>
            <select name="categoria_id" required>
                <?php foreach($data['categorias'] as $cat): ?>
                    <option value="<?= $cat->id_categoria ?>"><?= $cat->nombre ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Explica tu duda con detalle</label>
            <textarea name="contenido" style="height:150px;" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Publicar Pregunta</button>
        <a href="<?= BASE_URL ?>foro" style="display:block; text-align:center; margin-top:10px; font-size:0.9rem;">Cancelar</a>
    </form>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>