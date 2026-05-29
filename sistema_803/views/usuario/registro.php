<h1>Registrarse</h1>

<?php 
// Mensajes de éxito o error general
if(isset($_SESSION['register']) && $_SESSION['register'] == 'complete'): ?>
    <strong class="alert_green">Registro completado correctamente</strong>
<?php elseif(isset($_SESSION['register']) && $_SESSION['register'] == 'failed'): ?>
    <strong class="alert_red">Registro fallido, revisa los errores en el formulario</strong>
<?php endif; ?>
<?php Utils::deleteSession('register'); ?>

<?php 
// Recuperamos los errores del controlador si existen
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : null; 
?>

<form action="<?=base_url?>usuario/save" method="POST">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" required/>
    <?= isset($errors['nombre']) ? '<span class="error">'.$errors['nombre'].'</span>' : '' ?>
    
    <label for="apellidos">Apellidos</label>
    <input type="text" name="apellidos" required/>
    <?= isset($errors['apellidos']) ? '<span class="error">'.$errors['apellidos'].'</span>' : '' ?>
    
    <label for="email">Email</label>
    <input type="email" name="email" required/>
    <?= isset($errors['email']) ? '<span class="error">'.$errors['email'].'</span>' : '' ?>
    
    <label for="password">Contraseña</label>
    <input type="password" name="password" required/>
    <?= isset($errors['password']) ? '<span class="error">'.$errors['password'].'</span>' : '' ?>
    
    <label for="confirm_password">Confirmar Contraseña</label>
    <input type="password" name="confirm_password" required/>
    <?= isset($errors['confirm_password']) ? '<span class="error">'.$errors['confirm_password'].'</span>' : '' ?>
    
    <input type="submit" value="Registrarse" />
</form>

<?php 
// Limpiamos los errores después de mostrarlos
if(isset($_SESSION['errors'])){
    unset($_SESSION['errors']);
}
?>