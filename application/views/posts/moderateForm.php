<section class="container">
    <div class="posts">
        <?php if (count($items) > 0) {
           foreach ($items as $key => $row) { ?>
               <div class="<?= ($key == 0) ? "new_version" : ""?>">
                   <h1><?= ($key == 0) ? "Последняя версия" : "Версия сайта"?></h1>

                   <h2><?= $row['title']?></h2>
                   <span><?= $row['name']?></span>
                   <div>
                       <?php if (!empty($row['img'])) {?>
                           <img src="<?= $row['img']?>" />
                       <?php } ?>
                       <?= $row['text']?>
                   </div>
                   <div></div>
                   <span><?= $row['tags']?></span>
               </div>
        <? }
        } ?>

        <form class="moderate" method="post" action="/private_post/actionModerate">
            <input type="hidden" name="id" value="<?= $items[0]['id']?>">
            <button class="btn" name="moderate">Отмодерировать</button>
        </form>

    </div>
</section>