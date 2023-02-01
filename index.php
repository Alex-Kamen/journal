<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

/*$link = mysqli_connect("localhost", "root", "root", 'bsac_journal');

if ($link == false){
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else {
    print("Соединение установлено успешно");
}

$sql = 'SELECT * FROM `result` WHERE results LIKE "%font%"';

$result = mysqli_query($link, $sql);


while ($row = mysqli_fetch_array($result)) {
	$r = preg_replace('/(<font style=\\\"vertical-align: inherit;\\\">)|(<\/font>)/', '', $row['results']);

	echo "<pre>";
	//print_r($r);
	echo "</pre>";

    $sql = "UPDATE result SET results='".$r."' WHERE resultId=".$row['resultId'];

    echo "<pre>";
	print_r($sql);
	echo "</pre>";

    mysqli_query($link, $sql);

    $a = 'SELECT * FROM `result` WHERE resultId='.$row['resultId'];
    $res = mysqli_query($link, $a);

    print_r(mysqli_fetch_array($res));
}


$sql = 'SELECT * FROM `result` WHERE results LIKE "%font%"';

$result = mysqli_query($link, $sql);


while ($row = mysqli_fetch_array($result)) {
	echo "<pre>";
	print_r($row['results']);
	echo "</pre>";
}

echo "done";
if ($result == false) {
    print("Произошла ошибка при выполнении запроса");
}*/

define('ROOT', dirname(__FILE__));
require_once(ROOT.'/components/Router.php');

$router = new Router();
$router->run();