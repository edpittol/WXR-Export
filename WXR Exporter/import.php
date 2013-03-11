<?php


header('Content-type: application/json');
header('Content-Disposition: attachment; filename="wxrsettings.json"');

$content = file_get_contents($_FILES['file']['tmp_name']);

if(json_decode($content)) {
	echo $content;
} else {
	echo '{"error": true, "message":"Invalid File"}';
}