<?php

function connect($connection_info) {
	$con = @mysql_connect($connection_info['host'], $connection_info['user'], $connection_info['password']);
	if(!$con) {
		return false;
	} else {
		$db = @mysql_select_db($connection_info['database'], $con);
		if(!$db) {
			return false;
		} else {
			mysql_query("SET NAMES utf8");
			return true;
		}
	}
}

/**
 * Verify if the sql is valid and has results.
 * 
 * @param  $rs A resource to be validate.
 * @return boolean True if the result is valid and has results. False, otherwise.
 */
function validate_sql($rs) {
	return $rs && mysql_num_rows($rs);
}

/**
 * Return all permitted elements for a type.
 * 
 * @param string $type The type of the elements.
 * @return array The elements.
 */
function get_elements($type) {
	switch( $type ) {
	
		case 'wp:category' :
			return array("wp:category_nicename", "wp:category_parent", "wp:cat_name");
			break;
		case 'wp:tag' :
			return array("wp:tag_slug", "wp:cat_name");
			break;
		case 'wp:term' :
			return array("wp:term_taxonomy", "wp:cat_name", "wp:term_slug", "wp:term_parent", "wp:term_name");
			break;
		case 'item' :
			return array("title", "link", "description", "source", "enclosure", "category", "pubDate", "guid", "dc:author",
			"content:encoded", "excerpt:encoded", "wp:post_id", "wp:post_date", "wp:post_date_gmt", "wp:comment_status", "wp:ping_status",
			"wp:post_name", "wp:status", "wp:post_parent", "wp:menu_order", "wp:post_type", "wp:post_password", "wp:is_sticky",
			"wp:ping_status", "wp:ping_status", "wp:attachment_url", "wp:postmeta");
			break;
		default :
			return array();
			break;
	}
}

/**
 * Get colums of a mysql result set.
 * 
 * @param string $query The query.
 * @return boolean|array False if has a mysql erros or array with the columns.
 */
function get_columns($rs) {
	if(!validate_sql($rs)) return array();
	
	$fields = array();
	for($i = 0; $i < mysql_num_fields($rs); $i++) {
		$field = mysql_fetch_field($rs, $i);
		$fields[] = $field->name;
	}
	
	return $fields;
}

/**
 * Used to compare array of mapping by level.
 * 
 * @param unknown_type $a
 * @param unknown_type $b
 * @return number
 */
function level_cmp($a, $b)
{
	if ( ! isset($a['level'], $b['level']) || $a['level'] == $b['level'] ) {
		return 0;
	}
	return ( $a['level'] > $b['level'] ) ? -1 : 1;
}