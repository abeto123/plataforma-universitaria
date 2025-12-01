<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<!-- 1. BANNER DE BIENVENIDA (HERO) -->
<div class="hero-banner" style="background-color: rgba(176, 0, 0, 1);">
    <h1>Bienvenido a la Plataforma Universitaria</h1>
    <p>Conecta ideas, colabora en proyectos y construye el futuro de la universidad junto a estudiantes de la UNJBG.</p>
    
    <?php if(!isset($_SESSION['usuario_id'])): ?>
        <a href="<?= BASE_URL ?>auth/registro" class="btn-cta">Únete Ahora</a>
    <?php else: ?>
        <a href="<?= BASE_URL ?>idea/crear" class="btn-cta">Publicar Idea</a>
    <?php endif; ?>
</div>

<!-- 2. GRID DE 3 COLUMNAS -->
<div class="dashboard-grid">
    
    <!-- TARJETA 1: ÚLTIMAS IDEAS -->
    <div class="dashboard-card">
        <div>
            <h3>Últimas Ideas</h3>
            <?php foreach($data['ideas'] as $idea): ?>
                <div class="list-item">
                    <!-- Título clickeable -->
                    <h4><a href="#"><?= $idea->titulo ?></a></h4>
                    <p>Por: <?= $idea->autor ?></p>
                </div>
            <?php endforeach; ?>
            
            <?php if(empty($data['ideas'])): ?>
                <p style="color:#999; font-style:italic;">No hay ideas registradas.</p>
            <?php endif; ?>
        </div>
        
        <a href="<?= BASE_URL ?>idea" class="btn-outline">Ver todas</a>
    </div>

    <!-- TARJETA 2: ÚLTIMAS NOTICIAS -->
    <div class="dashboard-card">
        <div>
            <h3>Últimas Noticias</h3>
            <?php foreach($data['noticias'] as $noticia): ?>
                <div class="list-item">
                    <h4><?= $noticia->titulo ?></h4>
                    <p>
                        <?= date('d/m/Y', strtotime($noticia->fecha_publicacion)) ?>
                        <br>
                        <?= substr($noticia->contenido, 0, 60) ?>...
                    </p>
                </div>
            <?php endforeach; ?>
            
            <?php if(empty($data['noticias'])): ?>
                <p style="color:#999; font-style:italic;">Sin noticias recientes.</p>
            <?php endif; ?>
        </div>

        <a href="<?= BASE_URL ?>noticia" class="btn-outline">Ver todas</a>
    </div>

    <!-- TARJETA 3: PROYECTOS ACTIVOS -->
    <div class="dashboard-card">
        <div>
            <h3>Proyectos Activos</h3>
            <?php foreach($data['proyectos'] as $proyecto): ?>
                <div class="list-item">
                    <h4><?= $proyecto->nombre ?></h4>
                    <p>Líder: <?= $proyecto->responsable ?></p>
                </div>
            <?php endforeach; ?>

            <?php if(empty($data['proyectos'])): ?>
                <p style="color:#999; font-style:italic;">No hay proyectos activos.</p>
            <?php endif; ?>
        </div>

        <a href="<?= BASE_URL ?>proyecto" class="btn-outline">Ver todos</a>
    </div>

</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>