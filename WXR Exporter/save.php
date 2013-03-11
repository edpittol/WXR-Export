<?php

header('Content-type: application/json');
header('Content-Disposition: attachment; filename="wxrsettings.json"');

echo json_encode($_POST);
