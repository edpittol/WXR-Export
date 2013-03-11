$(function() {
	
	var $mapping = $("#mapping").clone().removeAttr("id");
	updateChildOf();
	
	// get child informations to update after all mappings load
	var children = new Array();
	
	/**
	 * Return an object with information to connect the server.
	 */
	function connectionInfo() {
		return {
			host : $("#host").val(),
			user : $("#user").val(),
			password : $("#password").val(),
			database : $("#database").val()
		};
	}
	
	/**
	 * Create a new mapping rules. If passed settings, yet load values
	 */
	function addMapping(settings) {
		var index = $(".mapping").length;
		var $currentMap = $mapping.clone()
								.find(".title")
									.text("Mapping " + (index + 1))
								.end();
		$.data($currentMap[0], "index", index);
		$currentMap.insertBefore($("#add-mapping"));
		
		// add parameters if is not null
		if(settings != null) {
			// load values
			$currentMap.find(".sql").val(settings.sql).trigger("blur");
			$currentMap.find(".type").val(settings.type).trigger("change");
			$currentMap.find(".join-column-in").val(settings.joincolumnin);
			$currentMap.find(".join-column-out").val(settings.joincolumnout);
			$(settings.maps).each(function(i, item) {
				fixed = item.fixed == "true";
				addMap($currentMap.find(".maps"), item.column, item.element, fixed, item.metakey);
			});
			
			children.push({
				value : settings.childof,
				mapping : index,
				joincolumnin : settings.joincolumnin,
				joincolumnout : settings.joincolumnout,
			});
		}
		
	}
	
	/**
	 * Define options for "Is child of"
	 * 
	 * TODO when has a selected value, mantain this value
	 */
	function updateChildOf() {
		var options = new Array();
		$(".mapping").each(function(i, mapping) {
			options.push({
				value : i,
				label : $(mapping).find(".title").text(),
			});
		});
		
		setOptions($(".mapping").find(".child"), options, true);
		
		$(".mapping").each(function(i, mapping) {
			$(children).each(function(j, child) {
				if($.data(mapping, "index") == child.mapping && child.value != "" ) {
					$(mapping)
						.find(".child")
							.val(child.value)
							.trigger("change")
						.end()
						.find(".join-column-in")
							.val(child.joincolumnin)
						.end()
						.find(".join-column-out")
							.val(child.joincolumnout);
					
				}
			});
		});
	}
	
	/**
	 * Add a map in a mapping rules
	 */
	function addMap($elem, column, element, fixed, metakey) {		
		$map = 
			$("<div>")
				.addClass("map");
	
		if(fixed) {
			$map
				.append(
					$("<div>")
					.addClass("grid-5-12 alpha columnval")
					.append(
						$("<input>")
							.attr({
								class : "fixed-value",
								type : "text",
								value : column
							})
					)
				);
		} else {
			$map
				.append(
					$("<div>")
						.addClass("grid-5-12 alpha columnval")
						.text(column)
				);
		}
		
		$map
			.append(
				$("<div>")
					.addClass("grid-5-12 elementval")
					.text(element)
			)		
			.append(
				$("<div>")
					.addClass("grid-2-12 omega")
					.append(
						$("<img>")
							.attr({
								class : "remove button",
								alt : "Delete",
								src : "images/delete.png"
							})
					)
			);
		
		// add key input for meta
		if(element == "wp:postmeta") {
			$map.append(
				$("<div>")
					.addClass("postmeta")
					.append(
						$("<div>")
							.addClass("grid-5-12 alpha")
							.text("Key:")
					)
					.append(
						$("<div>")
							.addClass("grid-5-12 keyvalval")
							.append(
								$("<input>")
									.attr({
										class : "key",
										type : "text",
										value : (metakey) ? metakey : ""
									})
							)
					)
					.after(
						$("<div>")
							.addClass("clear")
					)
			);
		}
		
		$map.appendTo($elem);
	}
	
	/**
	 * Define select options for a conjunt of values.
	 */
	function setOptions($elements, values, blank, selected) {
		$($elements).each(function(i, item) {
			$(item).html("");
			if(blank) {
				$(item).append($("<option>").val("").text(""));
			}
			$(values).each(function(j, val) {
				if($.isPlainObject(val)) {
					$option = $("<option>").val(val.value).text(val.label);
					value = val.value;
				} else {
					$option = $("<option>").val(val).text(val);
					value = val;
				}
				$(item).append($option);
			});
		});
	}
	
	function getData() {
		
		var settings = {};
		
		settings.connectioninfo = connectionInfo();
		settings.filesize = $("#filesize").val();
		settings.startid = $("#startid").val();
		
		settings.mappings = new Array();
		$(".mapping").each(function(i, mapping) {
			
			var maps = new Array();
			$(this).find(".map").each(function(j, map) {
				var $fixed = $(map).find(".fixed-value");
				
				var fixed = $fixed.length > 0;
				var column = (fixed) ? $fixed.val() : $(map).find(".columnval").text();
				var element = $(map).find(".elementval").text();
				var metakey = (element == "wp:postmeta") ? $(map).find(".key").val() : false;
				
				maps.push({
					fixed : fixed,
					column : column,
					element : element,
					metakey : metakey
				});
			});
			
			var type = $(this).find(".type").val();
			var sql = $(this).find(".sql").val();
			var childof = $(this).find(".child").val();
			var joincolumnin = $(this).find(".join-column-in").val();
			var joincolumnout = $(this).find(".join-column-out").val();
			
			settings.mappings.push({
				type : type == null ? '' : type,
				maps : maps,
				sql : sql == null ? '' : sql,
				childof : childof == null ? '' : childof,
				joincolumnin : joincolumnin == null ? '' : joincolumnin,
				joincolumnout : joincolumnout == null ? '' : joincolumnout
			});
		});
		
		return settings;
	}
	
	$("#upload").submit(function(e) {
		e.preventDefault();
		
		// clear children
		children = new Array();
		
		$(this).ajaxSubmit({
			success : function(responseText, statusText, xhr, $form) {
				// TODO substitute for messages in a fancy modal
				if(responseText.error != undefined) {
					console.log(responseText.message);
				} else {
					var settings = responseText;
					
					$("#host").val(settings.connectioninfo.host);
					$("#user").val(settings.connectioninfo.user);
					$("#password").val(settings.connectioninfo.password);
					$("#database").val(settings.connectioninfo.database);
					$("#filesize").val(settings.filesize);
					$("#startid").val(settings.startid);
					
					if(settings.mappings.length > 0) {
						$(".mapping").remove();
						$(settings.mappings).each(function(i, item) {
							addMapping(item);
						});	
					} else {
						addMapping();
					}

					updateChildOf();
					
					console.log("Setting imported.");					
				}
			}
		}); 
		
	}); 
	
	$(".mapping .remove-mapping").live("click", function() {
		$(this).parents(".mapping").remove();
		updateChildOf();
	});
	
	$(".type").live("change", function() {
		$element = $(this).parents(".mapping").find(".element");
		
		// Need a async function because it necessary work with the results when import a file
		$.ajax({
			type : "GET",
			url : "elements.php",
			data : { 
				type : $(this).val()
			},
			success : function(elements) {
				setOptions($element, elements);
			},
			dataType : "json",
			async : false
		});
	});	
	
	$(".child").live("change", function() {		
		
		var htmlin = $(this).parents(".mapping").find(".column").html();
		
		var htmlout = "";
		if($(this).val() != "") {
			$(".mapping").each(function(i, mapping) {
				if($.data(mapping, "index") == $(this).val()) {
					htmlout = $(mapping).find(".column").html();
					return;
				}
			});
		}
		
		$(this)
			.parents(".mapping")
			.find(".join-column-in")
				.html(htmlin)
			.end()
			.find(".join-column-out")
				.html(htmlout);
		
		
	});
	
	$(".sql").live("blur", function() {
		if($(this).val() != "") {
			var $error = $(this).parent().find(".sql-error");
			var $column = $(this).parents(".mapping").find(".column");
			
			// Need a async function because it necessary work with the results when import a file
			$.ajax({
				type : "POST",
				url : "columns.php",
				data : { 
					query : $(this).val(),
					connectioninfo : connectionInfo()
				},
				success : function(data) {
					if(data.error) {
						$error.show().find("h3").text(data.message);
					} else {
						$error.hide();
						data.columns.push("Fixed Value");
						setOptions($column, data.columns);
					}
				},
				dataType : "json",
				async : false
			});
		}
	});
	
	$(".add").live("click", function() {
		var $child = $(this).parents(".mapoptions");
		var column = $child.find(".column").val();
		var element = $child.find(".element").val();
		
		if(!(column == "" || element == "")) {
			var fixed = false;
			if(column == "Fixed Value") {
				column = "";
				fixed = true;
			}
			
			addMap($child.find(".maps"), column, element, fixed);
		}
	});
	
	$(".remove").live("click", function() {
		$(this).parent().parent().remove();
	});
	
	$("#add-mapping").click(function() {
		// FIXME When enter here change the child of mapping
		addMapping();
		updateChildOf();
	});
	
	$("#save").click(function(e) {
		e.preventDefault();
		$.download("save.php", getData());
	});
	
	$("#submit").click(function(e) {
		e.preventDefault();
		$.download("process.php", getData());
	});
	
});