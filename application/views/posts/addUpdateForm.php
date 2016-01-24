
<section class="container">
    <div class="posts">
        <h1>Форма добавления/редактирования поста</h1>
        <form method="post" action="<?=$method?>" class="main" ENCTYPE="multipart/form-data">
            <?= (!empty($errors) && array_key_exists('0', $errors) && strlen($errors['0']) > 0)
                ? $errors['0'] : ''?>
            <p>
                <input class="ClsRequired clsStringFrom3" type="text" name="title" value="<?= (isset($data['title'])) ? $data['title'] : ''?>" placeholder="Заголовок*">
            </p>
            <?= (!empty($errors) && array_key_exists('title', $errors) && !empty($errors['title']))
                ? $errors['title'] : ''?>

            <p>
                <textarea class="ClsRequired clsStringTo2000" type="text" name="text" placeholder="Текст поста*"><?= (isset($data['text'])) ? $data['text'] : ''?></textarea>
            </p>
            <?= (!empty($errors) && array_key_exists('text', $errors) && !empty($errors['text']))
                ? $errors['text'] : ''?>

            <p><input type="file" name="img" id="fileImg" value=""></p>
            <?= (!empty($errors) && array_key_exists('img', $errors) && !empty($errors['img']))
                ? $errors['img'] : ''?>

            <div id="fileDisplayArea">
                <? if(isset($data['img'])):?>
                    <img width="200" src="/uploads/posts/<?=$data['img']?>">
                    <input type="hidden" name="img" value="<?=(isset($data['img'])) ? $data['img'] : ''?>">
                <? endif;?>
            </div>

            <p>
                <input type="text" name="tags" value="<?= (isset($data['tags'])) ? $data['tags'] : ''?>" placeholder="Теги">
            </p>
            <?= (!empty($errors) && array_key_exists('login', $errors) && strlen($errors['login']) > 0)
                ? $errors['login'] : ''?>


            <p><input type="hidden" name="id" value="<?= (isset($data['id'])) ? $data['id'] : ''?>"></p>

            <p class="submit">
                <input type="submit" name="save" value="Сохранить" onclick="return false;">
            </p>
        </form>
    </div>
</section>