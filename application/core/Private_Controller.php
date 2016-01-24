<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Private_Controller extends MY_controller {

    function __construct(){
        parent::__construct();

        if(!$this->author_model->isUserAuthorize()){
            show_error('Страница не доступна для не авторизированого автора.');
            redirect('/', 'refresh');
        }

    }
}