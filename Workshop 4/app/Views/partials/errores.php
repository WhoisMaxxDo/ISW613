<?php if ($errores): ?>
<div class="alert" role="alert"><ul>
    <?php foreach ($errores as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
</ul></div>
<?php endif; ?>
