<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<h2>Registro de Estudiante</h2>

<?php if(!empty($data['error'])): ?>
    <div class="alert"><?= $data['error'] ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>auth/registro" method="POST">
    <div class="form-group">
        <label>Nombre Completo</label>
        <input type="text" name="nombre" required>
    </div>
    <div class="form-group">
        <label>Correo Institucional</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" required>
    </div>
    <div class="form-group">
        <label>Carrera Profesional</label>
        <select name="carrera" required>
            <option value="">-- Seleccione --</option>
            <?php foreach($data['carreras'] as $carrera): ?>
                <option value="<?= $carrera->id_carrera ?>"><?= $carrera->nombre ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn">Registrarse</button>
</form>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>
