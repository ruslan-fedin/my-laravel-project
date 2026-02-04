<?php
$start = microtime(true);
echo "<h1>Тест скорости</h1>";
echo "Время загрузки PHP: " . (microtime(true) - $start) . " секунд<br>";
echo "Память: " . memory_get_usage() / 1024 . " KB<br>";
echo "Путь: " . __FILE__ . "<br>";
