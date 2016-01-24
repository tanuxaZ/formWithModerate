<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Public_post
 * Контроллер включает методы, которые доступны как авторизированым
 * так и не авторизированым пользователям|авторам
 */
class Public_post extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('post_model');
    }

    /**
     * Возвращает страницу со списком постов
     */
    public function index()
    {
       // $this->load->library('pagination');

        $params = array(
            'page' => 1,
            'limit' => $this->post_model->getNumPostsPerPage(),
            'sort_field' => $this->post_model->getPKField(),
            'sort_order' => 'ASC'
        );

        $filter = array(
            'is_moderate' => 1
        );

        $data = $this->post_model->getListPosts($params, $filter);

        /*$config['base_url'] = '/public_post';
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $params['limit'];

        $this->pagination->initialize($config);

        echo $this->pagination->create_links();*/
        $this->showView('posts/list', $data);
    }

    public function getOne($id)
    {
        if (!$id) {
            echo 'Страница не найдена';
        } else {
            $data = $this->post_model->getOne($id);
            $this->showView('posts/onePost', $data);
        }
    }
}