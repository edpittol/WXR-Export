<?php

if(!isset($_GET['type'])) {
	die("{}");
}

require_once 'functions.php';

echo json_encode(get_elements($_GET['type']));