<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="form-box" style="max-width: 800px;">
    <h2 style="text-align: center; color: var(--primary); margin-bottom: 20px;">Redactar Nueva Noticia</h2>
    
    <form action="<?= BASE_URL ?>noticia/crear" method="POST">
        
        <div class="form-group">
            <label>Titular de la Noticia</label>
            <input type="text" name="titulo" placeholder="Ej: Apertura de inscripciones para Semillero de IA" required style="font-size: 1.2rem; padding: 15px;">
        </div>

        <div class="form-group">
            <label>Tipo de Publicación</label>
            <select name="tipo" required style="padding: 10px;">
                <option value="general">📰 General</option>
                <option value="convocatoria">📢 Convocatoria</option>
                <option value="semillero">🌱 Semillero de Investigación</option>
                <option value="voluntariado">🤝 Voluntariado</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Cuerpo de la Noticia</label>
            <textarea name="contenido" style="height: 300px; font-family: sans-serif; line-height: 1.5;" required placeholder="Escribe aquí toda la información..."></textarea>
            <small style="color: #666;">Puedes escribir texto largo, se respetarán los párrafos.</small>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="flex: 2; padding: 15px;">🚀 Publicar Noticia</button>
            <a href="<?= BASE_URL ?>noticia" class="btn" style="flex: 1; background: #ddd; color: #333; text-align: center; padding-top: 15px;">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>