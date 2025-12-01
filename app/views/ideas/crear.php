<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="form-box">
    <h2 style="text-align:center; margin-bottom:20px;">Publicar Nueva Idea</h2>
    
    <form action="<?= BASE_URL ?>idea/crear" method="POST">
        <div class="form-group">
            <label>Título de la Idea</label>
            <input type="text" name="titulo" placeholder="Ej: App de Delivery Universitario" required>
        </div>
        
        <div class="form-group">
            <label>Descripción Detallada</label>
            <textarea name="descripcion" placeholder="Explica en qué consiste tu idea..." required></textarea>
        </div>
        
        <div style="display:flex; justify-content:space-between;">
            <a href="<?= BASE_URL ?>idea" style="padding:10px;">Cancelar</a>
            <button type="submit" class="btn btn-primary">Publicar Idea</button>
        </div>
    </form>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
