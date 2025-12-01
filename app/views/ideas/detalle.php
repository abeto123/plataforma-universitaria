<?php require_once APPROOT . '/views/layouts/header.php'; ?>

<div class="container" style="margin-top: 30px;">
    
    <!-- Botón volver -->
    <a href="<?= BASE_URL ?>idea" style="color: #666;">&larr; Volver al banco de ideas</a>

    <div style="display: flex; flex-wrap: wrap; gap: 30px; margin-top: 20px;">
        
        <!-- COLUMNA IZQUIERDA: INFORMACIÓN DE LA IDEA -->
        <div style="flex: 2; min-width: 300px;">
            <div class="card">
                <span class="badge" style="background:#e3f2fd; color:#007bff; float:right;">
                    <?= strtoupper($data['idea']->estado) ?>
                </span>
                <h1 style="color: var(--primary); margin-bottom: 15px;"><?= $data['idea']->titulo ?></h1>
                
                <p style="font-size: 1.1rem; line-height: 1.8; color: #333;">
                    <?= nl2br($data['idea']->descripcion) ?>
                </p>

                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #777;">
                    <p>Propuesto por: <strong><?= $data['idea']->autor ?></strong></p>
                    <p>Carrera: <?= $data['idea']->carrera ?></p>
                    <p>Fecha: <?= date('d/m/Y', strtotime($data['idea']->fecha_creacion)) ?></p>
                </div>
            </div>

            <!-- BOTÓN DE VOTOS / APOYO -->
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <a href="<?= BASE_URL ?>idea/votar/<?= $data['idea']->id_idea ?>" 
                    class="btn" 
                    style="background: <?= $data['ya_vote'] ? '#e74c3c' : '#eee' ?>; 
                            color: <?= $data['ya_vote'] ? 'white' : '#333' ?>; 
                            border: 1px solid #ccc; padding: 5px 15px; display: flex; align-items: center; gap: 5px;">
                    <?= $data['ya_vote'] ? '♥ Apoyado' : '♡ Apoyar Idea' ?>
                    </a>
                <?php else: ?>
                    <span style="background: #eee; padding: 5px 15px; border-radius: 5px; color: #666;">♡ Ingresa para apoyar</span>
                <?php endif; ?>
                
                <strong style="font-size: 1.1rem; color: #555;">
                    <?= $data['votos'] ?> apoyos
                </strong>
            </div>

            
            <!-- SECCIÓN DE COMENTARIOS -->
            <div class="card" style="margin-top: 30px; border-left: 5px solid #6c757d;">
                <h3>💬 Comentarios y Feedback</h3>
                
                <!-- Listado -->
                <div style="margin-bottom: 20px; max-height: 400px; overflow-y: auto;">
                    <?php if(empty($data['comentarios'])): ?>
                        <p style="color: #999; font-style: italic;">No hay comentarios aún. Sé el primero en opinar.</p>
                    <?php else: ?>
                        <?php foreach($data['comentarios'] as $com): ?>
                            <div style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <strong style="color: var(--primary);"><?= $com->nombre_completo ?></strong>
                                    <small style="color: #999;"><?= date('d/m/Y H:i', strtotime($com->fecha_comentario)) ?></small>
                                </div>
                                <p style="margin: 5px 0; color: #333; line-height: 1.5;"><?= nl2br($com->comentario) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Formulario -->
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <form action="<?= BASE_URL ?>idea/comentar/<?= $data['idea']->id_idea ?>" method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                        <textarea name="comentario" style="width: 100%; height: 60px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" placeholder="Escribe una sugerencia o pregunta..." required></textarea>
                        <div style="text-align: right; margin-top: 10px;">
                            <button type="submit" class="btn btn-primary" style="font-size: 0.85rem;">Enviar Comentario</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div style="background: #e9ecef; padding: 10px; text-align: center; border-radius: 5px;">
                        <a href="<?= BASE_URL ?>auth/login" style="font-weight: bold;">Inicia sesión</a> para dejar un comentario.
                    </div>
                <?php endif; ?>
            </div>
            
        </div>

        <!-- COLUMNA DERECHA: EQUIPO Y ACCIONES -->
        <div style="flex: 1; min-width: 250px;">
            <!-- PANEL DE ADMINISTRACIÓN (SOLO PARA EL CREADOR) -->
            <?php if(isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $data['idea']->usuario_creador_id): ?>
                
                <div class="card" style="border-left: 5px solid #ffc107; background: #fffbe6;">
                    <h3 style="color: #bfa900;">👑 Panel de Gestión</h3>
                    
                    <!-- 1. CAMBIAR ESTADO DE LA IDEA -->
                    <form action="<?= BASE_URL ?>idea/cambiar_estado/<?= $data['idea']->id_idea ?>" method="POST" style="margin-bottom: 20px;">
                        <label style="font-size: 0.9rem; font-weight: bold;">Estado del Proyecto:</label>
                        <div style="display: flex; gap: 5px; margin-top: 5px;">
                            <select name="estado" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; flex-grow: 1;">
                                <option value="abierta" <?= $data['idea']->estado == 'abierta' ? 'selected' : '' ?>>Abierta</option>
                                <option value="en_desarrollo" <?= $data['idea']->estado == 'en_desarrollo' ? 'selected' : '' ?>>En Desarrollo</option>
                                <option value="cerrada" <?= $data['idea']->estado == 'cerrada' ? 'selected' : '' ?>>Culminada</option>
                            </select>
                            <button type="submit" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Guardar</button>
                        </div>
                    </form>
                    <!-- BOTÓN DE CONVERSIÓN A PROYECTO -->
                    <!-- Solo mostrar si la idea NO está cerrada -->
                    <?php if($data['idea']->estado != 'cerrada'): ?>
                        
                        <hr style="border: 0; border-top: 1px solid #e0d8a0; margin: 15px 0;">
                        
                        <div style="text-align: center;">
                            <p style="font-size: 0.85rem; color: #856404; margin-bottom: 5px;">
                                ¿El equipo está listo y la idea aprobada?
                            </p>
                            <form action="<?= BASE_URL ?>idea/convertir_a_proyecto/<?= $data['idea']->id_idea ?>" method="POST" onsubmit="return confirm('¿Estás seguro? Esto creará un Proyecto Oficial y cerrará esta Idea.');">
                                <button type="submit" class="btn" style="background: linear-gradient(45deg, #1d976c, #93f9b9); color: #004d26; font-weight: bold; width: 100%; border: 1px solid #1d976c;">
                                    🚀 Lanzar como Proyecto Oficial
                                </button>
                            </form>
                        </div>

                    <?php endif; ?>

                    <hr style="border: 0; border-top: 1px solid #e0d8a0; margin: 15px 0;">

                    <!-- 2. GESTIONAR SOLICITUDES -->
                    <h4>Solicitudes Pendientes (<?= count($data['solicitudes']) ?>)</h4>
                    
                    <?php if(empty($data['solicitudes'])): ?>
                        <p style="font-size: 0.85rem; color: #888;">No hay solicitudes nuevas.</p>
                    <?php else: ?>
                        <ul style="list-style: none; margin-top: 10px;">
                            <?php foreach($data['solicitudes'] as $solicitud): ?>
                                <li style="background: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; border: 1px solid #ddd;">
                                    <strong><?= $solicitud->nombre_completo ?></strong>
                                    <div style="font-size: 0.8rem; color: #666;"><?= $solicitud->carrera ?></div>
                                    
                                    <!-- Botones de Acción -->
                                    <div style="margin-top: 8px; display: flex; gap: 5px;">
                                        <a href="<?= BASE_URL ?>idea/gestionar_solicitud/<?= $data['idea']->id_idea ?>/<?= $solicitud->id_usuario ?>/aceptado" 
                                        class="btn" style="background: #28a745; color: white; padding: 3px 8px; font-size: 0.75rem;">✔ Aceptar</a>
                                        
                                        <a href="<?= BASE_URL ?>idea/gestionar_solicitud/<?= $data['idea']->id_idea ?>/<?= $solicitud->id_usuario ?>/rechazado" 
                                        class="btn" style="background: #dc3545; color: white; padding: 3px 8px; font-size: 0.75rem;">✖ Rechazar</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
            
            <!-- TARJETA DE ACCIÓN -->
            <div class="card" style="text-align: center;">
                <h3>¿Te interesa?</h3>

                <?php if($data['idea']->estado == 'abierta'): ?>
                    <p>Únete al equipo para desarrollar este proyecto.</p>
                <?php else: ?>
                    <p>El equipo de trabajo ya está cerrado.</p>
                <?php endif; ?>

                <?php if(isset($_SESSION['usuario_id'])): ?>
                    
                    <?php if($data['idea']->usuario_creador_id == $_SESSION['usuario_id']): ?>
                        <!-- CASO 1: ERES EL DUEÑO -->
                        <button class="btn" style="background: #ccc; cursor: not-allowed; width: 100%;" disabled>Eres el creador</button>
                    
                    <?php elseif($data['es_miembro']): ?>
                        <!-- CASO 2: YA ERES MIEMBRO -->
                        <button class="btn" style="background: #28a745; color: white; cursor: default; width: 100%;">Solicitud enviada ✔</button>
                    
                    <?php elseif($data['idea']->estado != 'abierta'): ?>
                        <!-- CASO 3: LA IDEA YA NO ADMITE GENTE (En desarrollo o Cerrada) -->
                        <button class="btn" style="background: #e9ecef; color: #666; cursor: not-allowed; width: 100%;" disabled>
                            🔒 Convocatoria Cerrada
                        </button>

                    <?php else: ?>
                        <!-- CASO 4: ESTÁ ABIERTA Y PUEDES UNIRTE -->
                        <a href="<?= BASE_URL ?>idea/unirse/<?= $data['idea']->id_idea ?>" class="btn btn-primary btn-block">Unirme al Equipo</a>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- NO LOGUEADO -->
                    <a href="<?= BASE_URL ?>auth/login" class="btn btn-primary btn-block">Inicia Sesión para Unirte</a>
                <?php endif; ?>
            </div>

            <!-- LISTA DE MIEMBROS -->
            <div class="card">
                <h3>Equipo de Trabajo</h3>
                <?php if(empty($data['miembros'])): ?>
                    <p style="color: #999;">Aún no hay miembros unidos.</p>
                <?php else: ?>
                    <ul style="list-style: none;">
                        <?php foreach($data['miembros'] as $miembro): ?>
                            <li style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                <strong><?= $miembro->nombre_completo ?></strong><br>
                                <small style="color: #666;"><?= $miembro->carrera ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/footer.php'; ?>