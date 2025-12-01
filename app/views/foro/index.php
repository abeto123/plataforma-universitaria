<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container" style="margin-top:20px;">
    
    <div style="display:flex; gap:30px; flex-wrap:wrap;">
        
        <!-- SIDEBAR IZQUIERDO (Filtros) -->
        <div style="flex:1; min-width:250px;">
            <?php if(isset($_SESSION['usuario_id'])): ?>
                <a href="<?= BASE_URL ?>foro/crear" class="btn btn-block" style="background:#007bff; color:white; margin-bottom:20px;">
                    ✍ Hacer una Pregunta
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>auth/login" class="btn btn-block" style="background:#6c757d; color:white; margin-bottom:20px;">
                    Logueate para preguntar
                </a>
            <?php endif; ?>

            <div class="card">
                <h4>Categorías</h4>
                <ul style="margin-top:10px;">
                    <li style="margin-bottom:8px;">
                        <a href="<?= BASE_URL ?>foro/index" style="color:<?= !$data['filtro_cat'] ? '#007bff' : '#555' ?>;">
                            Todas las categorías
                        </a>
                    </li>
                    <?php foreach($data['categorias'] as $cat): ?>
                        <li style="margin-bottom:8px;">
                            <a href="<?= BASE_URL ?>foro/index?categoria=<?= $cat->id_categoria ?>" 
                               style="color:<?= $data['filtro_cat'] == $cat->id_categoria ? '#007bff' : '#555' ?>;">
                                <?= $cat->nombre ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL (Lista de Preguntas) -->
        <div style="flex:3; min-width:300px;">
            
            <!-- Buscador -->
            <form action="<?= BASE_URL ?>foro/index" method="GET" style="margin-bottom:20px; display:flex;">
                <input type="text" name="busqueda" placeholder="¿Qué duda tienes hoy?" value="<?= $data['busqueda'] ?>" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px 0 0 5px;">
                <button type="submit" class="btn btn-primary" style="border-radius:0 5px 5px 0;">Buscar</button>
            </form>

            <?php if(empty($data['preguntas'])): ?>
                <div class="card" style="text-align:center; padding:40px;">
                    <h3 style="color:#888;">No se encontraron preguntas.</h3>
                    <p>Sé el primero en preguntar algo.</p>
                </div>
            <?php else: ?>
                <?php foreach($data['preguntas'] as $pregunta): ?>
                    <div class="card" style="border-left: 4px solid #17a2b8; margin-bottom:15px; padding:15px;">
                        <div style="display:flex; justify-content:space-between;">
                            <h4 style="margin:0;"><a href="<?= BASE_URL ?>foro/detalle/<?= $pregunta->id_foro_publicacion ?>" style="color:#333;"><?= $pregunta->titulo ?></a></h4>
                            <span class="badge" style="background:#e3f2fd; color:#007bff;"><?= $pregunta->categoria ?></span>
                        </div>
                        <p style="font-size:0.9rem; color:#666; margin:5px 0;">
                            <?= substr($pregunta->contenido, 0, 150) ?>...
                        </p>
                        <div style="font-size:0.8rem; color:#999; display:flex; justify-content:space-between; margin-top:10px;">
                            <span>Por: <strong><?= explode(' ', $pregunta->autor)[0] ?></strong></span>
                            <span>💬 <?= $pregunta->num_respuestas ?> respuestas</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>