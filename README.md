# formWithModerate

Перед выполнением выполнить ряд действий:

1) Создать пустую базу данных
2) В файле /application/config/database.php - поменять настройки базы
3) Запустить /migrate - запускает миграции в которых создаються все нужные таблицы
4) В файле /application/config/autoload.php в строке 61 закоментировать подключение сессий
$autoload['libraries'] = array('database', 'migration'/*, 'session'*/);
поменять на $autoload['libraries'] = array('database', 'migration', 'session');
