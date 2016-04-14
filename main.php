<?php

require_once 'autoload.php';

$app = new Application();

while($app->on) {
	$app->mainMenu();
}

?>