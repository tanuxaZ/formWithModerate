<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Вызов нужного отображения
     *
     * @param $path - имя файла отображение (например: "posts/postList" или "postList")
     * @param array $array - массив значений передаваемых в отображение
     */
    public function showView($path, $array = array())
    {
        $this->load->view('header');
        $this->load->view($path, $array);
        $this->load->view('footer');
    }

    /**
     * Заносит ошибки валидации формы и заполненные поля в сессию
     * делает редирект, если указан путь
     *
     * @param array $errors - массив ошибок
     * @param null $redirect_path - путь для редиректа
     */
    public function setErrors(Array $errors, $redirect_path = null)
    {
        $this->session->set_flashdata('errors', $errors);
        $this->session->set_flashdata('fields', $this->input->post(NULL,TRUE));

        if ($redirect_path) {
            redirect($redirect_path, 'refresh');
        }
    }

    /**
     * Возвращает массив ошибок из сессии
     *
     * @return array
     */
    public function getErrors()
    {
        return ($this->session->flashdata('errors'))
            ? $this->session->flashdata('errors')
            : '';
    }

    /**
     * Возвращает массив полей из сессии
     *
     * @return array
     */
    public function getFields()
    {
        return $this->session->flashdata('fields');
    }

    /**
     * Возвращает true - если ошибки есть, иначе false
     *
     * @return bool
     */
    public function isError()
    {
        if ($this->session->flashdata('errors')) {
            return true;
        }

        return false;
    }
}