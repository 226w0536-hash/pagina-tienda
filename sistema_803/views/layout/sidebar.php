<aside id="lateral">
    <div id="login" class="block_aside">
        
        <?php if(isset($_SESSION['error_login'])): ?>
            <strong class="alert_red">Login incorrecto: <?= $_SESSION['error_login'] ?></strong>
            <?php Utils::deleteSession('error_login'); ?>
        <?php endif; ?>

        <?php if(!isset($_SESSION['identity'])): ?>
            <h3>Entrar a la web</h3>
            <form action="<?=base_url?>usuario/login" method="post">
                <label for="email">Email</label>
                <input type="email" name="email" required />
                <label for="password">Contraseña</label>
                <input type="password" name="password" required />
                <input type="submit" value="Enviar" />
            </form>
        <?php else: ?>
            <h3><?=$_SESSION['identity']->nombre?> <?=$_SESSION['identity']->apellidos?></h3>
        <?php endif; ?>

        <ul>
            <?php if(isset($_SESSION['admin'])): ?>
                <li><a href="<?=base_url?>categoria/index">Gestionar categorías</a></li>
                <li><a href="<?=base_url?>producto/gestion">Gestionar productos</a></li>
                <li><a href="<?=base_url?>pedido/gestion">Gestionar pedidos</a></li>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['identity'])): ?>
                <li><a href="<?=base_url?>pedido/mis_pedidos">Mis pedidos</a></li>
                <li><a href="<?=base_url?>usuario/logout">Cerrar sesión</a></li>
            <?php else: ?> 
                <li><a href="<?=base_url?>usuario/registro">Registrate aqui</a></li>
            <?php endif; ?> 
        </ul>
    </div>

    <div class="block_aside">
        <h3>Categorías</h3>
        <ul>
            <?php $categorias = Utils::showCategorias(); ?>
            <?php while($cat = $categorias->fetch_object()): ?>
                <li>
                    <a href="<?=base_url?>producto/categoria?id=<?=$cat->id?>"><?=$cat->nombre?></a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</aside>
