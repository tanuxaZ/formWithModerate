<section class="container">
    <div class="posts">
        <div class="posts_list">
            <h1>Список постов для модератора</h1>
            <?php if (count($items) > 0) { ?>
                <ul>
                    <?php foreach ($items as $post) { ?>
                        <li>
                            <div>
                                <a href="/private_post/getModerateForm/<?= $post['id']?>"><?= $post['title']?></a>
                            </div>
                            <hr>
                        </li>
                    <?php }?>
                </ul>
            <?php }?>
        </div>
    </div>
</section>
