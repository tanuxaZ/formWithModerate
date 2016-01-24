    <section class="container">
    <div class="posts">
        <div class="posts_list">
            <h1>Список постов</h1>

            <?php if (isset($author) && $author == 1) { ?>
                <a href="/private_post/addPost"><div class="btn">Создать пост</div></a>
            <? }?>

            <?php if (count($items) > 0) { ?>
                <ul>
                    <?php foreach ($items as $post) { ?>
                        <li>
                            <div>
                                <a href="/public_post/getOne/<?= $post['id']?>"><?= $post['title']?></a>

                                <?php if (array_key_exists('isModerate', $post) && $post['isModerate'] == 0) {?>
                                    <span class="red">NOT MODERATE</span>
                                <? }?>
                            </div>
                            <?php if (isset($author) && $author == 1) { ?>
                                <div>
                                    <a href="/private_post/updatePost/<?= $post['id']?>">Редактировать</a>
                                    <span class="a" onclick="delPost('<?= $post['id']?>')">Удалить</span>
                                </div>
                            <? }?>
                            <hr>
                        </li>
                    <?php }?>
                </ul>
            <?php }?>
        </div>
    </div>
</section>
