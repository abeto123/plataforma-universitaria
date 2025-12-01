<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<h2>Iniciar Sesión</h2>

<?php if(!empty($data['error'])): ?>
    <div class="alert"><?= $data['error'] ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>auth/login" method="POST">
    <div class="form-group">
        <label>Correo Electrónico</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" style="background-color: #a00 ; color: #fff;" class="btn">Ingresar</button>
</form>

<p>¿No tienes cuenta? <a href="<?= BASE_URL ?>auth/registro">Regístrate aquí</a></p>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
