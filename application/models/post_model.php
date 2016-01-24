<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Class Post_model
 *  Класс для работы с постами
 */

class Post_model extends Base_model
{
    const MAIN_TABLE_NAME = 'posts_main';
    const MAIN_PRIMARY_KEY = 'id';
    const MODERATE_TABLE_NAME = 'posts_moderate';
    const IMAGE_PATH = './uploads/posts/';
    const NUM_POSTS_PER_PAGE = 10;

    const ADD_UPDATE_CONFIG = array(
        array(
            'field' => 'title',
            'label' => 'Название',
            'rules' => 'trim|required|min_length[3]|max_length[150]',
        ),
        array(
            'field' => 'text',
            'label' => 'Содержание поста',
            'rules' => 'trim|required|min_length[10]|max_length[2000]'
        )
    );

    const POSTS_IMG_CONFIG = array(
        'upload_path' => self::IMAGE_PATH,
        'allowed_types' => 'jpg|png'
    );

    /**
     * Конструктор
     * Определяет оснойвную таблицу с которой работаем
     * и первичный ключ
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = self::MAIN_TABLE_NAME;
        $this->primary_key = self::MAIN_PRIMARY_KEY;
    }

    /**
     * Возвращает к-тво постов на одной странице в списке
     *
     * @return int
     */
    public function getNumPostsPerPage()
    {
        return self::NUM_POSTS_PER_PAGE;
    }

    /**
     * Возвращает название PK
     *
     * @return string
     */
    public function getPKField()
    {
        return self::MAIN_PRIMARY_KEY;
    }

    /**
     * Возвращает конфигурацию для валидации формы добавления/редактирования
     *
     * @return array
     */
    public function getAddUpdateConfig()
    {
        return self::ADD_UPDATE_CONFIG;
    }

    /**
     * Возвращает конфигурации для валидации загружаемых картинок в пост
     *
     * @return array
     */
    public function getPostsImageConfig()
    {
        return self::POSTS_IMG_CONFIG;
    }

    /**
     * Метод для создания нового поста
     * Новая запись заности в главную таблицу с флагом is_moderate = 0
     * и во вторую таблицу для модерирования
     *
     * @param array $insert - массив полей поста
     * @return bool|int - возвращает false, если произошла ошибка,
     * иначе - идентификатор добавленной записи
     */
    public function create(Array $insert)
    {
        if (count($insert) == 0 ||
            !array_key_exists('title', $insert) ||
            !array_key_exists('text', $insert) ||
            !array_key_exists('author_id', $insert)) {
            return false;
        }

        $this->db->insert(self::MAIN_TABLE_NAME, $insert);

        $insert[self::MAIN_PRIMARY_KEY] = $this->db->insert_id();
        $this->db->insert(self::MODERATE_TABLE_NAME, $insert);

        return $insert[self::MAIN_PRIMARY_KEY];
    }

    /**
     * Метод для редактирования записей
     * отредактированная запись заносится в таблицу для модерации
     *
     * @param $id - идентификатор записи
     * @param array $update - массив значений
     * @param bool $isUpdateModerateTable - определяет в какой таблице делать update,
     * по умолчанию в главной
     * @return bool -
     */
    public function update($id, Array $update, $isUpdateModerateTable = false)
    {
        if (!$id ||
            count($update) == 0 ||
            !array_key_exists('title', $update) ||
            !array_key_exists('text', $update)
        ) {
            return false;
        }

        $tableName = self::MAIN_TABLE_NAME;

        if ($isUpdateModerateTable) {
            $tableName = self::MODERATE_TABLE_NAME;
        }

        return $this->insertUpdate($update, $tableName);
    }

    /**
     * Удаление поста
     * Удаляем картинки, привязаные к главной таблице и к таблице модерации
     * Удаляем запись тоже в двух таблицах
     *
     * @param $id - идентификатор поста
     * @param bool|false $onlyModerate - если true - удаляем только из таблиці модерации, иначе - false
     * @param bool|string $mainImg - имя картинки, которую нужно удалить, если модерируем
     * @return bool - true, если все ок, иначе - false
     */
    public function delete($id, $onlyModerate = false, $mainImage = false)
    {
        $this->load->helper('file');

        if (!$id) {
            return false;
        }

        if ($onlyModerate && $mainImage) {
            $imgInModerate = $this->get($id, array(), 'img', array(),self::MODERATE_TABLE_NAME)['img'];
            $delImgModerate = true;
            if ($imgInModerate !== $mainImage) {
                $delImgMain = $this->deleteImg(self::MAIN_TABLE_NAME, $id, $mainImage);
            } else {
                $delImgMain = true;
            }
        } else {
            $delImgModerate = $this->deleteImg(self::MODERATE_TABLE_NAME, $id);
            $delImgMain = $this->deleteImg(self::MAIN_TABLE_NAME, $id);
        }

        if ($delImgModerate && $delImgMain) {
            if (!$this->db->delete(self::MODERATE_TABLE_NAME, array(self::MAIN_PRIMARY_KEY => $id))) {
                return false;
            }

            if (!$onlyModerate) {
                if (!$this->db->delete(self::MAIN_TABLE_NAME, array(self::MAIN_PRIMARY_KEY => $id))) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Модерация поста
     *
     * @param $id - идентификатор поста
     * @return bool - true, если модерация успешна,
     * иначе - false
     */
    public function moderate($id)
    {
        if (!$id) {
            return false;
        }

        $update = $this->get($id, array(), '', array(), self::MODERATE_TABLE_NAME);
        $mainImg = $this->get($id, array(), 'img', array(),self::MODERATE_TABLE_NAME)['img'];

        $update['is_moderate'] = 1;

        $this->db->trans_start();
        $this->update($id, $update);
        $this->delete($id, true, $mainImg);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return false;
        }

        return true;
    }

    /**
     * Список постов в зависимости от параметров
     *
     * @param array $params - массив параметров
     * @param array $filter - массив фильтров
     * @return array - массив найденіх значений
     */
    public function getListPosts(Array $params, Array $filter)
    {
        $select = "id, title";
        return $this->getList($params, $filter, array(), $select);
    }

    /**
 * Метод возвращает список постов для одного автора
 *
 * @return array
 */
    public function getListForAuthor()
    {
        $query = "SELECT
                    pm.id,
                    ifnull(p.title, pm.title) as title,
                    if(p.id is null, 1, 0) as isModerate
                FROM posts_main pm
                LEFT JOIN posts_moderate p on pm.id = p.id
                WHERE pm.author_id = " . $this->author_model->getUserID() .
                " ORDER BY pm.id DESC; ";

        $res = $this->db->query($query);

        if ($res && $res->num_rows() > 0) {
            $result['items'] = $res->result_array();
            $result['total'] = count($result['items']);
            return $result;
        }

        return array();
    }

    /**
     * Список постов в зависимости от параметров
     * для модератора
     *
     * @param array $params - массив параметров
     * @param array $filter - массив фильтров
     * @return array - массив найденых значений
     */
    public function getListForModerate(Array $params, Array $filter)
    {
        $select = "id, title";
        return $this->getList($params, $filter, array(), $select, self::MODERATE_TABLE_NAME);
    }

    /**
     * Получает массив данных для одного поста
     * используется для отмодерированых постов
     *
     * @param $id - идентификатор поста
     * @return array|bool - false, если возникла ошибка,
     * иначе - массив с данными
     */
    public function getOne($id)
    {
        if (!$id) {
            return false;
        }

        $join = array('authors', "$this->table_name.author_id = authors.id");
        $select = "$this->table_name.$this->primary_key, $this->table_name.title, $this->table_name.text,
                   $this->table_name.img, $this->table_name.author_id, $this->table_name.tags, authors.name";

        $where = array('is_moderate' => 1);

        $result = $this->get($id, $join, $select, $where);

        if (isset($result['img']) && !empty($result['img'])) {
            $result['img'] = substr(self::IMAGE_PATH, 1).$result['img'];
        }

        if (count($result) == 0) {
            show_error('Страница не найдена');
        }

        return $result;
    }

    /**
     * Метод возвращает запись на редактирование
     * если указан второй параметр, возвращает 2 записи для сравнения при модерации
     *
     * @param $id - идентификатор записи
     * @param bool|false $forModerateView - true, если хотим увидеть 2 записи из
     * главной табл и из таблицы для модерации
     * @return bool|array - false, если неудача, иначе массив значений
     */
    public function getOneForUpdate($id, $forModerateView = false)
    {
        if (!$id) {
            return false;
        }

        $query = "SELECT
                      p_mod.`id` as id,
                      p_mod.`title`,
                      p_mod.`text`,
                      p_mod.`img`,
                      p_mod.`author_id`,
                      p_mod.`tags`,
                      a.`name`
                  FROM `posts_moderate` p_mod
                  JOIN `authors` a ON p_mod.`author_id` = a.`id`
                  WHERE p_mod.`id` = " . (int)$id . " and a.id = " . $this->author_model->getUserID() .
                  " UNION ALL
                    SELECT
                        pm.`id` as id,
                        pm.`title`,
                        pm.`text`,
                        pm.`img`,
                        pm.`author_id`,
                        pm.`tags`,
                        a.`name`
                    FROM `posts_main` pm
                    JOIN `authors` a ON pm.`author_id` = a.`id`
                    WHERE pm.`id` = " . (int)$id . " and is_moderate = 1 and a.id = " . $this->author_model->getUserID();

        $res = $this->db->query($query);

        if ($res && $res->num_rows() > 0) {
            if (!$forModerateView) {
                $result = $res->row_array();
            } else {
                $result = $res->result_array();
            }
        }

        foreach ($result as $num => $row) {
            if (isset($row['img']) && !empty($row['img'])) {
                $result[$num]['img'] = substr(self::IMAGE_PATH, 1).$row['img'];
            }
        }

        return $result;
    }

    /**
     * Удаление картинки привязаной к записи в базе
     *
     * @param $tableName - имя таблицы
     * @param $pkName - название первичного ключа
     * @param $id - идентификатор записи
     * @return bool - true, если удалено успешно, иначе - false
     */
    private function deleteImg($tableName, $id, $mainImage = false)
    {
        if (!$mainImage) {
            $img_name_query = $this->db->select('img')
                ->where(self::MAIN_PRIMARY_KEY, $id)
                ->get($tableName);
            $img_name_row = $img_name_query->row_array()['img'];
        } else {
            $img_name_row = $mainImage;
        }

        if (!empty(trim($img_name_row))) {
            if (!delete_file(self::IMAGE_PATH.$img_name_row)) {
                return false;
            }
        }

        return true;
    }
}