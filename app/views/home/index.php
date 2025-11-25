<?php include '../app/views/layouts/header.php'; ?>

<div class="jumbotron">
    <h1 class="display-4">Bienvenido a la Plataforma Universitaria</h1>
    <p class="lead">Conecta ideas, colabora en proyectos y construye el futuro de la universidad.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a class="btn btn-primary btn-lg" href="<?php echo BASE_URL; ?>auth/register" role="button">Únete Ahora</a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Últimas Ideas</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($data['ideas'])): ?>
                    <?php foreach ($data['ideas'] as $idea): ?>
                        <div class="mb-3">
                            <h6><?php echo htmlspecialchars($idea['titulo']); ?></h6>
                            <small class="text-muted">Por: <?php echo htmlspecialchars($idea['creador_nombre']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay ideas disponibles.</p>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>ideas/index" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Últimas Noticias</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($data['noticias'])): ?>
                    <?php foreach ($data['noticias'] as $noticia): ?>
                        <div class="mb-3">
                            <h6><?php echo htmlspecialchars($noticia['titulo']); ?></h6>
                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay noticias disponibles.</p>
                <?php endif; ?>
                <a href="/plataforma_universitaria/noticias" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Proyectos Activos</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($data['proyectos'])): ?>
                    <?php foreach ($data['proyectos'] as $proyecto): ?>
                        <div class="mb-3">
                            <h6><?php echo htmlspecialchars($proyecto['nombre']); ?></h6>
                            <small class="text-muted">Por: <?php echo htmlspecialchars($proyecto['creador_nombre']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay proyectos activos.</p>
                <?php endif; ?>
                <a href="/plataforma_universitaria/proyectos" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
        </div>
    </div>
</div>

<?php include '../app/views/layouts/footer.php'; ?>