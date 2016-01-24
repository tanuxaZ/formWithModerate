<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Base_model extends CI_Model
{
    protected $table_name;
    protected $primary_key;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Возвразает данные одной строки из таблицы по идентификатору
     *
     * @param $id - идентификатор строки
     * @param array $join - массив пресоединенных таблиц вида array('table_name', 'prev_table_name.id=table_name.id')
     * @param array $where - массив условий
     * @param $select - строка для указания полей выборки вида 'id, name'
     * @param string $primaryKey - имя PK, если оно отлично от глобального
     * @param string $tableName - название таблицы, если оно отлично от глобального
     * @return array
     */
    public function get($id, $join = array(), $select = '', $where = array(), $tableName = '')
    {
        $primaryKey = $this->primary_key;

        if (empty($tableName)) {
            $tableName = $this->table_name;
        }

        if (count($join) > 0) {
            $this->db->join($join[0], $join[1]);
        }

        if (!empty($select)) {
            $this->db->select($select);
        }

        if (count($where) > 0) {
            $this->db->where($where);
        }

        $query = $this->db->where("$tableName.$primaryKey", (int)$id)->get($tableName);

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return array();
    }

    /**
     * 	Возвращает массив выбранных значений по параметрам
     *
     *  @param array $params - массив параметров
     *      page - активная страница пейджинга
     *      limit - количество записей на одной странице
     *      sort_field - поле для сортировки
     *      sort_order - ASC или DESC
     *  @param array $filters - массив фильтров вида
     *      ('is_moderate' => 1|0, 'name' => 'имя или часть имени')
     *  @param array $join - массив пресоединенных таблиц вида array('table_name', 'prev_table_name.id=table_name.id')
     *  @param $select - строка для указания полей выборки вида 'id, name'
     *  @return array - возвращает массив вида array('total' => 110, 'items' => array('Выбранные значения'))
     */
    public function getList(Array $params, Array $filters, Array $join = array(), $select = '*', $anotherTable = '')
    {
        $result = array();

        $tableName = $this->table_name;
        if (!empty($anotherTable)) {
            $tableName = $anotherTable;
        }

        $this->db->start_cache();

        if (count($filters) > 0) {
            $this->db->like($filters);
        }

        if (!empty($select)) {
            $this->db->select($select);
        }

        if (count($join) > 0) {
            $this->db->join($join[0], $join[1]);
        }

        $this->db->stop_cache();

        $this->db->order_by($params['sort_field'], $params['sort_order'])
            ->limit($params['limit'], ($params['page'] - 1) * $params['limit']);

        $query_items = $this->db->get($tableName);

        $result['items'] = $query_items->result_array();
        $result['total'] = $this->db->count_all_results($tableName);

        $this->db->flush_cache();

        return $result;
    }

    /**
     * Метод выплняет update, если дубликат ключа, иначе insert
     *
     * @param array $insertUpdate - массив значений
     * @param string $tableName - имя таблицы, указывать
     * если работем не с главной таблицей
     * @return mixed
     */
    public function insertUpdate(Array $insertUpdate, $tableName = '')
    {
        $fieldsName = array_keys($insertUpdate);
        $values = array_values($insertUpdate);

        $sql = "INSERT INTO ";

        if (!empty($tableName)) {
            $sql .= "$tableName ";
        } else {
            $sql .= "$this->table_name ";
        }

        $sql .= "(" . implode(',', $fieldsName) . ") VALUES (?";

        for ($value = 1; $value < count($values); ++$value) {
            $sql .= ", ?";
        }

        $sql .= ") ON DUPLICATE KEY UPDATE ";

        foreach ($fieldsName as $field) {
            $sql .= "$field = VALUES($field), ";
        }

        $sql = substr(trim($sql), 0, -1);

        return $this->db->query($sql, $values);
    }
}