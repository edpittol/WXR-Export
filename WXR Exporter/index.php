<?php

require_once 'functions.php';

include "header.php";

?>

<form id="upload" class="formee" action="import.php" enctype="multipart/form-data" method="post">
	<h3>Import</h3>
	<div class="grid-4-12 alpha">
		<input name="file" type="file" />
	</div>
	<div class="grid-3-12 omega">
		<input type="submit" value="Import" />
	</div>
</form>

<form class="formee" action="process.php" method="POST">
	
	<h3>Create</h3>
	
	<div id="form-header">
		<div class="grid-3-12 alpha">
			<label>Host:</label>
			<input id="host" type="text" />
		</div>
		
		<div class="grid-3-12">
			<label>User:</label>
			<input id="user" type="text" />
		</div>
		
		<div class="grid-3-12">
			<label>Password:</label>
			<input id="password" type="text" />
		</div>
		
		<div class="grid-3-12 omega">
			<label>Database:</label>
			<input id="database" type="text" />
		</div>
		
		<div class="grid-3-12 alpha">
			<label>File size (in KB):</label>
			<input id="filesize" type="text" />
		</div>
		
		<div class="grid-3-12 omega">
			<label>Start ID:</label>
			<input id="startid" type="text" />
		</div>
	</div>
	
	<div class="grid-12-12 alpha omega">
		<h3>Mappings</h3>
	</div>
	
	<div id="mapping" class="mapping">
	
		<div class="grid-12-12 alpha omega">
			<h3 class="title">Mapping 1</h3>
			<img class="remove-mapping button" alt="delete" src="images/delete.png" />
		</div>
	
		<div class="grid-6-12 alpha">
			<div class="grid-12-12 alpha omega">
				<label for="sql">SQL:</label>
				<textarea class="sql" class="session"></textarea>
				<div class="formee-msg-error sql-error alpha omega"><h3>Mensagem</h3></div>
			</div>
		
			
			<div class="grid-6-12 alpha">
				<label for="type">Type:</label>
				<select class="type">
					<option value=""></option>
					<option value="wp:category">wp:category</option>
					<option value="wp:tag">wp:tag</option>
					<option value="wp:term">wp:term</option>
					<option value="item">item</option>
				</select>
			</div>
			
			<div class="clear"></div>
		
			<div class="grid-6-12 alpha">
				<label for="type">Is child of:</label>
				<select class="child">
					<option value=""></option>
					<option value="0">Mapping 1</option>
				</select>
			</div>
		
			<div class="grid-6-12 omega">
				<label for="type">Join Column:</label>
				<div class="grid-2-12 alpha">
					<label for="type">In:</label>
				</div>
				<div class="grid-10-12 omega">
					<select class="join-column-in">
						<option value=""></option>
					</select>
				</div>
			</div>
		
			<div class="grid-6-12 alpha"></div>
		
			<div class="grid-6-12 omega">
				<div class="grid-2-12 alpha">
					<label for="type">Out:</label>
				</div>
				<div class="grid-10-12 omega">
					<select class="join-column-out">
						<option value=""></option>
					</select>
				</div>
			</div>
		</div>
		
		<div class="grid-6-12 omega mapoptions">
			<h3>Map</h3>
			
			<div class="grid-5-12 alpha">
				<label>Database column:</label>
				<select class="column">
					<option value=""></option>
				</select>
			</div>
		
			<div class="grid-5-12">
				<label>WXR element:</label>
				<select class="element">
					<option value=""></option>
				</select>
			</div>
			
			<div class="grid-2-12 omega withoutlabel">
				<img class="add button" alt="add" src="images/add.png" />
			</div>
			
			<div class="clear"></div>
	
			<div class="maps"></div>
		</div>
	</div>
	
	<div id="add-mapping" class="grid-12-12">
		<img id="add" class="button" alt="add" src="images/add.png" />
		Add Mapping
	</div>
	
	<div class="grid-12-12 alpha omega">
		<input id="save" class="formee-button" type="button" value="Save Settings" />
		<input id="submit" class="formee-button" type="submit" value="Process" />
	</div>
	
</form>



</body>
</html>