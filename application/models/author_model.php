<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Author_model
 * Класс для работы с авторами
 */
class Author_model extends Base_model
{
    const TABLE_NAME = 'authors';
    const PRIMARY_KEY = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->table_name = self::TABLE_NAME;
        $this->primary_key = self::PRIMARY_KEY;
    }

    /**
     * Метод возвращает true - если пользователь авторизирован, иначе - false
     * Пока не реализована авторизация/аутентификация возвращаем true
     *
     * @return bool
     */
    public function isUserAuthorize()
    {
        return true;
    }

    /**
     * Метод возвращает идентификатор авторизированого пользователя, если пользователь авторизирован,
     * иначе false
     *  Пока не реализована авторизация/аутентификация возвращаем пользователя с id=3
     *
     * @return int|bool
     */
    public function getUserID()
    {
        return 1;
    }

    /**
     * Метод возвращает true если пользователь является модератором, иначе true
     * Пока не реализована авторизация/аутентификация/распределение прав возвращаем true
     *
     * @return bool
     */
    public function isModerate()
    {
        return true;
    }

}