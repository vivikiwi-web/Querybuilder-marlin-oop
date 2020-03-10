# DB - PDO QueryBuilder

Предоставляет расширение для собственного PDO вместе с запросами и соединением к базе данных.

## Конфигурация и запуск DB - PDO QueryBuilder

- Класс DB принимает в себя массив с конфигурациями базы данных в таком формате:

  `'connection' => 'mysql:host=localhost',`<br>
  `'dbname' => 'your_db_name',`<br>
  `'username' => 'your_db_username',`<br>
  `'password' => 'your_db_password',`<br>
  `'charset' => 'utf8'`

- Для инициализации DB класса используется статический метод getInstance() которому передаётся массив с конфигурациями базы данных

  `$db = DB::getInstance($config);`

## Использование DB - PDO QueryBuilder

### query() - Выполнение всех запросов в базу данных с неподготовленным SQL запросом.

- Принимает такие параметры как:

  `query(string $sql [, array $params ])`

- Список параметров:

  sql - SQL запрос (тип строка). В качестве передаваемых данный всегда указываете знак "?".
  params - массив с параметрами для SQL запроса. В качестве параметров передаётся ключ (имя столбца в базе данных) и значение.

- Пример использования:

  `$sql = "SELECT * FROM users WHERE name = ? ORDER BY name";`<br>
  `$params = [ 'name' => 'user_name' ];`<br>
  `$users = $db->query($sql, $params);`

- Возвращаемые значения:
  В случае успешного завершения возвращает PDO объект с результатом, а так же со всеми атрибутами или TRUE в случае возникновения ошибки.

### results() - Возвращаем результат из выполненного SQL запроса. Этот медот использования после выполнения метода query()

- Не принимает никаких параметров.

- Пример использования:

  `$sql = "SELECT * FROM users WHERE name = ? ORDER BY name";`<br>
  `$params = [ 'name' => 'username' ];`<br>
  `$users = $db->query($sql, $params)->results();`

- Возвращаемые значения:

  В случае успешного завершения возвращает PDO объект с результатом или NULL в случае возникновения ошибки.

### findAll() - Подготовка SQL запроса для выполнения и вывода всех данных результата SQL запроса.

- Принимает такие параметры как:

  `findAll(string $table [, array $params ])`

- Список параметров:

  table - Название таблицы в вазе данных (тип строка).
  params - массив с параметрами для SQL запроса. В качестве параметров передаётся ключ (имя столбца в базе данных) и значение.

- Пример использования:

  `$params = ['name' => 'user_name', 'email' => 'user_email'];`<br>
  `$users = $db->findALl($table, $params);`<br>

- Возвращаемые значения:

  В случае успешного завершения возвращает PDO объект с результатом или FALSE в случае возникновения ошибки.

### insert() - Подготовка и выполниние SQL запроса для сохранения данных б базу данных

- Принимает такие параметры как:

  `insert(string $table [, array $params ])`

- Список параметров:

  table - Название таблицы в вазе данных (тип строка).
  params - массив с параметрами для SQL запроса. В качестве параметров передаётся ключ (имя столбца в базе данных) и значение.

- Пример использования:

  `$params = ['name' => 'email', 'email' => 'user_email'];`<br>
  `$users = $db->insert('users', $params);`

- Возвращаемые значения:

  В случае успешного завершения возвращает TRUE или FALSE в случае возникновения ошибки.

### update() - Подготовка и выполниние SQL запроса для обновления данных в базе данных по ID

- Принимает такие параметры как:

  `update(string $table, integer $id [, array $params ])`

- Список параметров:

  table - Название таблицы в вазе данных (тип строка).
  id - индификатор (тип число).
  params - массив с параметрами для SQL запроса. В качестве параметров передаётся ключ (имя столбца в базе данных) и значение.

- Пример использования:

  `$params = ['name' => 'email', 'email' => 'user_email'];`<br>
  `$id = 6;`<br>
  `$users = $db->updare('users', $id, $params);`

- Возвращаемые значения:

  В случае успешного завершения возвращает TRUE или FALSE в случае возникновения ошибки.

### delete() - Подготовка и выполниние SQL запроса для удаления данных из базе данных по ID

- Принимает такие параметры как:

  `query(string $table, integer $id)`

- Список параметров:

  table - Название таблицы в вазе данных (тип строка).
  id - индификатор (тип число).

- Пример использования:

  `$id = 6;`<br>
  `$users = $db->delete('users', $id);`

- Возвращаемые значения:

  В случае успешного завершения возвращает TRUE или FALSE в случае возникновения ошибки.

### find() - Подготовка SQL запроса для выполнения и вывода всех данных результата SQL запроса с дополнительными параметрами.

- Принимает такие параметры как:

  `find(string $table [, array $params ])`

- Список параметров:

  table - Название таблицы в вазе данных (тип строка).
  params - массив с параметрами для SQL запроса. В качестве параметров передаётся:

  - conditions - в параметр передаётся условие SQL запроса в качестве строки `name = ?` либо массив `['name = ?', 'email = ?']`
  - bind - в параметр передаётся массив данных SQL запроса `['user_name', 'user_email']`
  - order в параметр передаётся строка для сортировки данных SQL запроса `'name'`
  - limit в параметр передаётся число для ограничения количества вывода данных SQL запроса `1`

- Пример использования:

  `$params = [`<br>
  `'conditions' => ['name = ?', 'email = ?'],`<br>
  `'bind' => ["user_name", 'user_name'],`<br>
  `'order' => 'name',`<br>
  `'limit' => 2`<br>
  `];`<br>
  `$users = $db->find('users', $params);`

- Возвращаемые значения:

  В случае успешного завершения возвращает PDO объект с результатом или TRUE в случае возникновения ошибки.

### findFirst() - Подготовка SQL запроса для выполнения и вывода первой строки из результата SQL запроса с дополнительными параметрами.

- Принимает такие параметры как:

  `findFirst(string $table [, array $params ])`

- Список параметров:

  table - Название таблицы в вазе данных (тип строка).
  params - массив с параметрами для SQL запроса. В качестве параметров передаётся:

  - conditions - в параметр передаётся условие SQL запроса в качестве строки `name = ?` либо массив `['name = ?', 'email = ?']`
  - bind - в параметр передаётся массив данных SQL запроса `['user_name', 'user_email']`
  - order в параметр передаётся строка для сортировки данных SQL запроса `'name'`
  - limit в параметр передаётся число для ограничения количества вывода данных SQL запроса `1`

- Пример использования:

  `$params = [`<br>
  `'conditions' => ['name = ?', 'email = ?'],`<br>
  `'bind' => ["user_name", 'user_name'],`<br>
  `'order' => 'name',`<br>
  `'limit' => 2`<br>
  `];`<br>
  `$users = $db->findFirst('users', $params);`

- Возвращаемые значения:

  В случае успешного завершения возвращает PDO объект с результатом или TRUE в случае возникновения ошибки.

### first() - Возвращаем первый элемент из выполненного SQL запроса. Этот медот использования после выполнения метода query().

- Не принимает никаких параметров.

- Пример использования:

  `$users = $db->query('SELECT * FROM users ORDER BY name')->first();`<br>

- Возвращаемые значения:

  В случае успешного завершения возвращает PDO объект с результатом или пустой массив в случае если нету никаких данных.

### count() - Возвращаем количество элементов из выполненного SQL запроса. Этот медот использования после выполнения метода query().

- Не принимает никаких параметров.

- Пример использования:

  `$users = $db->query('SELECT * FROM users ORDER BY name')->count();`<br>

- Возвращаемые значения:

  В случае успешного завершения возвращает количество элементов или 0 в случае если нету никаких данных.

### lastID() - Возвращаем ID последнего встабленного элемнента из выполненного SQL запроса. Этот медот использования после выполнения метода query().

- Не принимает никаких параметров.

- Пример использования:

  `$users = $db->query('INSERT INTO users (name, email) VALUES (?,?)', ['name' => 'user_name', 'email' => 'user_email'])->lastID();`<br>

- Возвращаемые значения:

  В случае успешного завершения возвращает последний вставленный ID элементов или 0 в случае если нету никаких данных.

### getColumns() - Подготовка SQL запроса для вывода имён колонок таблицы из базы данных

- Принимает такие параметры как:

  `getColumns(string $table)`

- Список параметров:

  table - Название таблицы в вазе данных (тип строка).

- Пример использования:

  `$users = $db->getColumns('users');`<br>

- Возвращаемые значения:

  В случае успешного завершения возвращает массив данных с именами колонок и параметрами таблицы из базы данных или NULL в случае возникновения ошибки.
