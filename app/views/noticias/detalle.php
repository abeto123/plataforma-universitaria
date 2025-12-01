<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container" style="margin-top: 30px; max-width: 800px;">
    
    <a href="<?= BASE_URL ?>noticia" style="color: #666;">&larr; Volver a Noticias</a>

    <div class="card" style="margin-top: 20px; padding: 40px;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <span class="badge" style="background: #333; color: white; padding: 5px 15px; font-size: 0.9rem;">
                <?= strtoupper($data['noticia']->tipo) ?>
            </span>
            <h1 style="margin: 20px 0; color: var(--primary); font-size: 2.2rem;">
                <?= $data['noticia']->titulo ?>
            </h1>
            <div style="color: #888;">
                Por <strong><?= $data['noticia']->autor ?></strong> | <?= date('d \d\e F, Y', strtotime($data['noticia']->fecha_publicacion)) ?>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 30px;">

        <div style="font-size: 1.15rem; line-height: 1.8; color: #333; text-align: justify;">
            <?= nl2br($data['noticia']->contenido) ?>
        </div>

        <div style="margin-top: 50px; text-align: center;">
            <p style="font-weight: bold;">Comparte esta noticia:</p>
            <!-- Botones falsos de compartir para decoración -->
            <button class="btn" style="background: #983b3bff; color: white;">Facebook</button>
            <button class="btn" style="background: #d32525ff; color: white;">WhatsApp</button>
        </div>

    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>