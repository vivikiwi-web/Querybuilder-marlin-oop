<?php

/**
 * DB класс предназначен для подключения к базе данных и выполнения запросов
 */
class DB
{
    private static $_instance = null;
    private $_pdo, $_query, $error = false, $_result, $_count = 0, $_lastInsertID = null;

    /**
     * Инициализация объекта PDO и подключение к базе данных
     *
     * @param array $config
     */
    private function __construct(array $config)
    {

        try {
            $this->_pdo = new PDO(
                "{$config['connection']};dbname={$config['dbname']};charset={$config['charset']}",
                $config['username'],
                $config['password']
            );
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Проверка на инициализацию объекта PDO и инициализация самого объекта PDO
     *
     * @param array $config
     * @return PDO
     */
    public static function getInstance($config)
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB($config);
        }
        return self::$_instance;
    }

    /**
     * Выполнение всех запросов в базу данных
     *
     * @param string $sql
     * @param array  $params
     * @return void
     */
    public function query($sql, $params = [])
    {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if (!empty($params)) {
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
        }

        if ($this->_query->execute()) {
            $this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
            $this->_count = $this->_query->rowCount();
            $this->_lastInsertID = $this->_pdo->lastInsertId();
        } else {
            $this->_error = true;
        }
        return $this;
    }

    /**
     * Подготовка SQL запроса для метода find()
     *
     * @param string $table
     * @param array  $params
     * @return void
     */
    protected function _read($table, $params = [])
    {
        $conditionString = '';
        $bind            = [];
        $orderString     = [];
        $limitString     = [];

        // condition
        if (array_key_exists('conditions', $params)) {
            if (is_array($params['conditions'])) {
                foreach ($params['conditions'] as $condition) {
                    $conditionString .= ' ' . $condition . ' AND';
                }

                $conditionString = trim($conditionString);
                $conditionString = rtrim($conditionString, ' AND');
            } else {
                $conditionString .= $params['conditions'];
                $conditionString = trim($conditionString);
            }

            if ($conditionString != '') {
                $conditionString = ' WHERE ' . $conditionString;
            }
        }
        // bind
        if (array_key_exists('bind', $params)) {
            $bind = $params['bind'];
        }
        // order
        if (array_key_exists('order', $params)) {
            $orderString = ' ORDER BY ' . $params['order'];
        }
        // limit
        if (array_key_exists('limit', $params)) {
            $limitString = ' LIMIT ' . $params['limit'];
        }

        $sql = "SELECT * FROM {$table}{$conditionString}{$orderString}{$limitString}";

        if (!$this->query($sql, $bind)->getError()) {
            if (!count($this->_result)) return false;
            return true;
        }
        return false;
    }

    /**
     * Запрос в базу данных для поиска и выдачи всех найденных элементов
     *
     * @param string $table
     * @param array $params
     * @return void
     */
    public function find($table, $params = [])
    {
        if ($this->_read($table, $params)) {
            return $this->results();
        } else {
            return false;
        }
    }

    /**
     * Запрос в базу данных для поиска и выдачи первого найденного элемента
     *
     * @param string $table
     * @param array  $params
     * @return void
     */
    public function findFirst($table, $params = [])
    {
        if ($this->_read($table, $params)) {
            return $this->first();
        } else {
            return false;
        }
    }

    /**
     * Подготовка SQL запроса для выполнения и вывода всех даннуж результата SQL запроса 
     *
     * @param string $table
     * @param array  $fields
     * @return void
     */
    public function findAll($table, $fields = [])
    {
        $fieldString = '';
        $values = [];

        foreach ($fields as $field => $value) {
            $fieldString .= ' ' . $field . ' = ? AND';
            $values[] = $value;
        }

        $fieldString = rtrim($fieldString);
        $fieldString = rtrim($fieldString, ' AND');

        if ($fieldString != '') {
            $fieldString = 'WHERE ' . $fieldString;
        }

        $sql = "SELECT * FROM users {$fieldString}";

        if (!$this->query($sql, $values)->getError()) {
            return $this->_result;
        }
        return false;
    }

    /**
     * Подготовка и выполниние SQL запроса для сохранения данных б базу данных
     *
     * @param string $table
     * @param array  $fields
     * @return void
     */
    public function insert($table, $fields = [])
    {
        $fieldString = '';
        $valueString = '';
        $values = [];

        foreach ($fields as $field => $value) {
            $fieldString .= '`' . $field . '`,';
            $valueString .= '?,';
            $values[] = $value;
        }

        $fieldString = rtrim($fieldString, ',');
        $valueString = rtrim($valueString, ',');

        $sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";

        if (!$this->query($sql, $values)->getError()) {
            return true;
        }
        return false;
    }

    /**
     * Подготовка и выполниние SQL запроса для обновления данных в базе данных по ID
     *
     * @param string  $table
     * @param integer $id
     * @param array   $fields
     * @return void
     */
    public function update($table, $id, $fields = [])
    {
        $fieldString = '';
        $values = [];

        foreach ($fields as $field => $value) {
            $fieldString .= ' ' . $field . ' = ?,';
            $values[] = $value;
        }

        $fieldString = rtrim($fieldString);
        $fieldString = rtrim($fieldString, ',');

        $sql = "UPDATE {$table} SET {$fieldString} WHERE id = {$id}";

        if (!$this->query($sql, $values)->getError()) {
            return true;
        }
        return false;
    }

    /**
     * Подготовка и выполниние SQL запроса для удаления данных из базе данных по ID
     *
     * @param string  $table
     * @param integer $id
     * @return void
     */
    public function delete($table, $id)
    {
        $sql = "DELETE FROM {$table} WHERE id = {$id}";

        if (!$this->query($sql, $values)->getError()) {
            return true;
        }
        return false;
    }

    /**
     * Возвращаем результат из выполненного SQL запроса. Этот медот использования после выполнения метода query()
     *
     * @return void
     */
    public function results()
    {
        return $this->_result;
    }

    /**
     * Возвращаем первый элемент из выполненного SQL запроса
     *
     * @return void
     */
    public function first()
    {
        return (!empty($this->_result[0])) ? $this->_result[0] : [];
    }

    /**
     * Возвращаем количество элементов из выполненного SQL запроса
     *
     * @return void
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Возвращаем ID последнего встабленного элемнента из выполненного SQL запроса
     *
     * @return void
     */
    public function lastID()
    {
        return $this->_lastInsertID;
    }

    /**
     * Подготовка SQL запроса для вывода имён колонок таблицы из базы данных
     *
     * @param string $table
     * @return void
     */
    public function getColumns($table)
    {
        return $this->query("SHOW COLUMNS FROM {$table}")->results();
    }

    /**
     * Возвращаем информацию на наличие ошибок про выполнения SQL запроса 
     *
     * @return void
     */
    private function getError()
    {
        return $this->_error;
    }
}
