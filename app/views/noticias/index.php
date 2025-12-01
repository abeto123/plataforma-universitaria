<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Cartelera Informativa</h2>
    
    <!-- Botón visible SOLO para Administradores -->
    <?php if(isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] == 'administrador'): ?>
        <a href="<?= BASE_URL ?>noticia/crear" class="btn btn-primary" style="background:#0056b3;">📢 Publicar Anuncio</a>
    <?php endif; ?>
</div>

<!-- FILTROS TIPO PESTAÑA -->
<div style="margin-bottom: 30px; border-bottom: 2px solid #eee;">
    <?php 
        $tipos = [
            '' => 'Todas', 
            'convocatoria' => 'Convocatorias', 
            'semillero' => 'Semilleros', 
            'voluntariado' => 'Voluntariado', 
            'general' => 'General'
        ];
    ?>
    <?php foreach($tipos as $key => $label): ?>
        <a href="<?= BASE_URL ?>noticia/index<?= $key ? '?tipo='.$key : '' ?>" 
           style="display:inline-block; padding: 10px 20px; text-decoration:none; 
                  border-bottom: 3px solid <?= $data['filtro_tipo'] == $key ? '#b30000' : 'transparent' ?>;
                  color: <?= $data['filtro_tipo'] == $key ? '#b30000' : '#666' ?>; font-weight:bold;">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- LISTADO -->
<div class="dashboard-grid">
    <?php foreach($data['noticias'] as $noticia): ?>
        
        <?php 
            // Color de la etiqueta según el tipo
            $color = '#666';
            if($noticia->tipo == 'convocatoria') $color = '#e67e22'; // Naranja
            if($noticia->tipo == 'semillero') $color = '#27ae60';    // Verde
            if($noticia->tipo == 'voluntariado') $color = '#9b59b6'; // Morado
            if($noticia->tipo == 'general') $color = '#3498db';      // Azul
        ?>

        <div class="card" style="border-left: 5px solid <?= $color ?>;">
            <div style="margin-bottom:10px;">
                <span class="badge" style="background:<?= $color ?>; color:white;">
                    <?= strtoupper($noticia->tipo) ?>
                </span>
                <span style="color:#999; font-size:0.85rem; float:right;">
                    <?= date('d/m/Y', strtotime($noticia->fecha_publicacion)) ?>
                </span>
            </div>
            
            <h3 style="margin:10px 0; font-size:1.4rem;">
                <a href="<?= BASE_URL ?>noticia/detalle/<?= $noticia->id_noticia ?>" style="color:#333;">
                    <?= $noticia->titulo ?>
                </a>
            </h3>
            
            <p style="color:#666; line-height:1.6;">
                <?= substr($noticia->contenido, 0, 180) ?>...
            </p>
            
            <div style="margin-top:15px; border-top:1px solid #eee; padding-top:10px; font-size:0.9rem; color:#888;">
                Publicado por: <?= $noticia->autor ?>
            </div>
        </div>

    <?php endforeach; ?>

    <?php if(empty($data['noticias'])): ?>
        <p style="text-align:center; width:100%; grid-column: 1 / -1; color:#999;">No hay noticias en esta categoría.</p>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>