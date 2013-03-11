<?php

if(!isset($_POST['query'], $_POST['connectioninfo'])) {
	die("{}");
}

require_once 'functions.php';

$connect = connect( $_POST['connectioninfo'] );

$rs = mysql_query( $_POST['query'] );

if( ! ( $connect && validate_sql( $rs ) ) ) {
	$error = true;
	$message = mysql_errno() . " - " . mysql_error();
	$columns = array();
} else {
	$error = false;
	$message = "";
	$columns = get_columns( $rs );
}

echo json_encode( array(
	"error" => $error,
	"message" => $message,
	"columns" => $columns
) );