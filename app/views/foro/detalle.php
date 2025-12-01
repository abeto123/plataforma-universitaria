<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container" style="margin-top:20px;">
    <a href="<?= BASE_URL ?>foro" style="color:#666;">&larr; Volver al Foro</a>

    <!-- PREGUNTA PRINCIPAL -->
    <div class="card" style="margin-top:15px; border-top:5px solid #007bff;">
        <h1 style="font-size:1.8rem; margin-bottom:10px;"><?= $data['pregunta']->titulo ?></h1>
        <div style="margin-bottom:15px;">
            <span class="badge" style="background:#e3f2fd; color:#007bff;"><?= $data['pregunta']->categoria ?></span>
            <span style="font-size:0.85rem; color:#888; margin-left:10px;">
                Publicado por <strong><?= $data['pregunta']->autor ?></strong> el <?= date('d/m/Y', strtotime($data['pregunta']->fecha_publicacion)) ?>
            </span>
        </div>
        <p style="font-size:1.1rem; line-height:1.6; color:#333;">
            <?= nl2br($data['pregunta']->contenido) ?>
        </p>
    </div>

    <h3 style="margin:30px 0 15px 0;">Respuestas (<?= count($data['respuestas']) ?>)</h3>

    <!-- LISTA DE RESPUESTAS -->
    <?php foreach($data['respuestas'] as $resp): ?>
        <div class="card" style="padding:15px; background:#f9f9f9; border-left:none; border:1px solid #eee;">
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong><?= $resp->autor ?> <small style="font-weight:normal; color:#666;">(<?= $resp->rol ?>)</small></strong>
                <small style="color:#999;"><?= date('d/m/Y H:i', strtotime($resp->fecha_respuesta)) ?></small>
            </div>
            <p style="color:#444;"><?= nl2br($resp->contenido) ?></p>
        </div>
    <?php endforeach; ?>

    <?php if(empty($data['respuestas'])): ?>
        <p style="color:#888; font-style:italic;">Nadie ha respondido aún. ¡Sé el primero!</p>
    <?php endif; ?>

    <!-- FORMULARIO DE RESPUESTA -->
    <?php if(isset($_SESSION['usuario_id'])): ?>
        <div class="card" style="margin-top:30px; border-left:5px solid #28a745;">
            <h4>Tu Respuesta</h4>
            <form action="<?= BASE_URL ?>foro/responder/<?= $data['pregunta']->id_foro_publicacion ?>" method="POST">
                <textarea name="contenido" style="width:100%; height:100px; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:4px;" placeholder="Escribe tu solución aquí..." required></textarea>
                <button type="submit" class="btn" style="background:#28a745; color:white;">Publicar Respuesta</button>
            </form>
        </div>
    <?php else: ?>
        <div style="background:#e9ecef; padding:15px; border-radius:5px; text-align:center; margin-top:20px;">
            <a href="<?= BASE_URL ?>auth/login" style="font-weight:bold;">Inicia sesión</a> para responder.
        </div>
    <?php endif; ?>

</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>