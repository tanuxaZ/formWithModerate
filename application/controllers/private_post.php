<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Private_post extends Private_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('post_model');
    }

    /**
     * Вывод страницу со списком постов авторизированого автора
     */
    public function getListForAuthor()
    {
        $data = $this->post_model->getListForAuthor();
        $data['author'] = 1;
        $this->showView('posts/list', $data);
    }

    /**
     * Вывод страницы со списком постов для модератора
     */
    public function getListForModerate()
    {
        if (!$this->author_model->isModerate()) {
            show_error('Страница доступна только для модераторов.');
        }

        $params = array(
            'page' => 1,
            'limit' => $this->post_model->getNumPostsPerPage(),
            'sort_field' => $this->post_model->getPKField(),
            'sort_order' => 'DESC'
        );

        $data = $this->post_model->getListForModerate($params, array());

        $this->showView('posts/listForModerate', $data);
    }

    /**
     * Выводит форму добавления нового поста
     */
    public function addPost()
    {
        $result = array('method' => '/private_post/actionAdd');

        if ($this->isError()) {
            $result['errors'] = $this->getErrors();
            $result['data'] = $this->getFields();
        }

        $this->showView('/posts/addUpdateForm', $result);
    }

    /**
     * Выводит форму редактирование поста
     */
    public function updatePost($id)
    {
        $result = array('method' => '/private_post/actionUpdate');
        $result['data'] = $this->post_model->getOneForUpdate($id);

        if ($this->isError()) {
            $result['errors'] = $this->getErrors();
            $result['data'] = $this->getFields();
        }

        $this->showView('/posts/addUpdateForm', $result);
    }

    /**
     * Метод обработки формы сохранения нового поста
     *
     */
    public function actionAdd()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->post_model->getAddUpdateConfig());

        if ($this->form_validation->run()) {

            if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
                $this->load->library('upload', $this->post_model->getPostsImageConfig());
                if (!$this->upload->do_upload('img')) {
                    $errors = array('0' => '<div class="error">' . $this->upload->display_errors() . '</div>');
                    $this->setErrors($errors, '/private_post/addPost');
                } else {
                    $data['img'] = $this->upload->data()['file_name'];
                }
            }
            $data['title'] = $this->input->post('title',TRUE);
            $data['text'] = $this->input->post('text',TRUE);
            $data['tags'] = $this->input->post('tags',TRUE);
            $data['author_id'] = $this->author_model->getUserID();

            if (!$this->post_model->create($data)) {
                $errors = array('0' => '<div class="error">При сохранении возникла ошибка.</div>');
                $this->setErrors($errors, '/private_post/addPost');
            }
        } else {
            $errors = array(
                'title' => form_error('title', '<div class="error">', '</div>'),
                'text' => form_error('text', '<div class="error">', '</div>')
            );

            $this->setErrors($errors, '/private_post/addPost');
        }
        redirect('/private_post/getListForAuthor');
    }

    /**
     * Метод для обработки формы редактирования поста
     */
    public function actionUpdate()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->post_model->getAddUpdateConfig());
        $id = $this->input->post('id', TRUE);
        if ($this->form_validation->run()) {

            $data['img'] = ($this->input->post('img', TRUE));

            if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
                $this->load->library('upload', $this->post_model->getPostsImageConfig());
                if (!$this->upload->do_upload('img')) {
                    $errors = array('0' => '<div class="error">' . $this->upload->display_errors() . '</div>');
                    $this->setErrors($errors, "/private_post/updatePost/$id");
                } else {
                    $data['img'] = $this->upload->data()['file_name'];
                }
            }

            $data['title'] = $this->input->post('title', TRUE);
            $data['text'] = $this->input->post('text', TRUE);
            $data['tags'] = $this->input->post('tags', TRUE);
            $data['id'] = $id;
            $data['author_id'] = $this->author_model->getUserID();

            if (!$this->post_model->update($id, $data, true)) {
                $errors = array('0' => '<div class="error">При сохранении возникла ошибка.</div>');
                $this->setErrors($errors, "/private_post/updatePost/$id");
            }
        } else {
            $errors = array(
                'title' => form_error('title', '<div class="error">', '</div>'),
                'text' => form_error('text', '<div class="error">', '</div>')
            );

            $this->setErrors($errors, "/private_post/updatePost/$id");
        }

        redirect('/private_post/getListForAuthor');
    }

    /**
     * Получает данные для формы модерации
     *
     * @param $id - идентификатор поста
     * @return mixed -  массив или ошибку
     */
    public function getModerateForm($id)
    {
        if (!$this->author_model->isModerate()) {
            show_error('Страница доступна только для модераторов.');
        }

        if (!$id) {
            show_error('Запись не найдена.');
        }

        $res = $this->post_model-> getOneForUpdate($id, true);

        if (count($res) == 0) {
            show_error('Запись не найдена.');
        }

        $this->showView('posts/moderateForm.php', array('items' => $res));
    }

    /**
     * Метод обработки формы модерации
     */
    public function actionModerate()
    {
        if (!$this->author_model->isModerate()) {
            show_error('Не хватает прав для выполнения действия.');
        }

        $id = $this->input->post('id', TRUE);

        if (!$this->post_model->moderate($id)) {
            $errors = array('0' => '<div class="error">При модерации возникла ошибка.</div>');
            $this->setErrors($errors, "/private_post/getModerateForm/$id");
        }

        redirect('/private_post/getListForModerate', 'refresh');
    }

    public function actionDelete($id)
    {
        $authorId = $this->post_model->get($id, array(), 'author_id')['author_id'];

        if ($authorId != $this->author_model->getUserID()) {
            show_error("Недостаточно прав для удаления.");
        }

        if (!$this->post_model->delete($id)) {
            show_error("При удалении возникла ошибка.");
        }

        redirect('/private_post/getListForAuthor', 'refresh');
    }
}
