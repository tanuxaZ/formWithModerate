<section class="container">
    <div class="posts">
        <a href="/">Вернуться в список</a>
        <h2><?= $title?></h2>
        <span><?= $name?></span>
        <div>
            <?php if (!empty($img)) {?>
                <img src="<?= $img?>" />
            <?php } ?>
            <?= $text?>
        </div>
        <div style="clear: both"></div>
        <span><?= $tags?></span>
    </div>
</section>