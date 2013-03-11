<?php

ini_set('max_execution_time', 300);

ob_start();

require_once 'lib/wxrapi/WXRExport.class.php';
require_once 'functions.php';

$filename = "wxrexport.zip";
$export = new WXRExport( $filename, $_POST['filesize'] );

if( ! connect( $_POST['connectioninfo'] ) ) {
	die( mysql_error() );
}

$postid = $_POST['startid'];

// verify dependency of child. First process the parents
foreach( $_POST['mappings'] as $i => $mapping ) {
	if( ! isset( $_POST['mappings'][$i]['level'] ) ) {
		$_POST['mappings'][$i]['level'] = 0;
	}
	
	
	if( $mapping['childof'] != "" && $mapping['joincolumnout'] ) {
		$_POST['mappings'][$mapping['childof']]['parents'][$mapping['joincolumnout']] = array();
		
		// increase the level if the level of the parent is less ou equal than the child
		if($_POST['mappings'][$mapping['childof']]['level'] <= $_POST['mappings'][$i]['level']) {
			$_POST['mappings'][$mapping['childof']]['level'] = $_POST['mappings'][$i]['level'] + 1;
		}	
	}
	
	// save the current position of mapping, to maintain the relationship
	$_POST['mappings'][$i]['index'] = $i;
}

usort($_POST['mappings'], "level_cmp");

echo "Dependencies:\n\n";
foreach( $_POST['mappings'] as $i => $mapping ) {
	echo "Mapping {$mapping['index']}:\n";
	echo "\tSQL: {$mapping['sql']}\n";
	echo "\tChild of: {$mapping['childof']}\n";
	echo "\tJoin Column In: {$mapping['joincolumnin']}\n";
	echo "\tJoin Column Out: {$mapping['joincolumnout']}\n";
	echo "\tLevel: {$mapping['level']}\n";
}
echo "\n";


echo "Processing:\n\n";
// TODO eliminate invalid elements for the type
foreach( $_POST['mappings'] as $i => $mapping ) {
	echo "Mapping {$mapping['index']}\n";
	
	$rs = mysql_query( $mapping['sql'] );
		
	if ( ! validate_sql( $rs ) ) continue;

	$type = $mapping['type'];
	$elements = get_elements( $type );
	
	// create an array if have maps
	$maps_metakeys = array();
	$maps_elements = array();
	foreach( $mapping['maps'] as $map ) {
		
		$values = array(
			"column" => $map['column'],
			"fixed" => $map['fixed'] == "true",
			"metakey" => $map['metakey'] == "false" ? false : $map['metakey'],
		);
		
		if($map['element'] == "wp:postmeta") {
			$maps_metakeys[] = $values;
		} else {
			$maps_elements[$map['element']] = $values;
		}
	}

	switch( $type ) {
	
		case 'wp:category' :
			// TODO implement category
			break;
		case 'wp:tag' :
			// TODO implement tag
			break;
		case 'wp:term' :
			// TODO implement term
			break;
		case 'item' :
			// TODO implement comments
			
			if( $mapping['joincolumnin'] && $mapping['joincolumnout'] ) {
				$joincolumnin = $mapping['joincolumnin'];
				$joincolumnout = $mapping['joincolumnout'];
			} else {
				$joincolumnin = false;
				$joincolumnout = false;
			}
			
			// load the parents posts of this mapping if have
			$parents = false;
			if( $mapping['childof'] != "" ) {
				echo "Child of: {$mapping['childof']}\n";
				foreach( $_POST['mappings'] as $j => $mapping_parent ) {
					if($mapping_parent['index'] == $mapping['childof'] && isset( $mapping_parent['parents'] ) ) {
						$parents = $mapping_parent['parents'];
						echo "Parents: \n";
						var_dump($parents);
					}
				}
			}
			
			while($data = mysql_fetch_assoc($rs)) {				
				$elements = array();
				$metakeys = array();
				
				foreach( $maps_elements as $element => $map ) {
					$elements[$element] = ($map['fixed']) ? $map['column'] : $data[$map['column']];
				}
				
				foreach( $maps_metakeys as $element => $map ) {
					$metakeys[$map['metakey']] = ($map['fixed']) ? $map['column'] : $data[$map['column']];
				}
				
				$elements['wp:post_id'] = $postid;
				
				// add column value in relation the join column out
				if( isset( $_POST['mappings'][$i]['parents'] ) ) {
					foreach( $_POST['mappings'][$i]['parents'] as $key => $value) {
						$_POST['mappings'][$i]['parents'][$key][$data[$key]] = $postid;
					}
				}
				
				if( $parents ) {					
					$elements['wp:post_parent'] = $parents[$joincolumnout][$data[$joincolumnin]];
				}
				
				$postid++;
				
				$export->addItem( $elements, $metakeys );
				
 				echo "Type: {$elements['wp:post_type']} - Id: {$elements['wp:post_id']}";
 				if(isset($elements['wp:post_parent'])) {
 					echo " - Parent: {$elements['wp:post_parent']}";
 				}
 				
 				echo "\n";
			}
			break;
	}
}

$file = $export->create_zip();


