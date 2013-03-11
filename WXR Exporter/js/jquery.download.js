/**
 * http://filamentgroup.com/lab/jquery_plugin_for_requesting_ajax_like_file_downloads/
 * Modified by Eduardo Pittol
 */
jQuery.download = function(url, data, method){
	
	function createInput(key, value) {
		
		if(typeof value == 'object') {		
			var inputs = '';
			$.each(value, function(idx, val) {
				inputs += createInput(key + "[" + idx + "]", val);
			});
			return inputs;
		} else {
			return '<input type="hidden" name="'+ key +'" value="'+ value +'" />';
		}
	}
	
	//url and data options required
	if( url && data ){ 
		//split params into form inputs
		var inputs = '';
		$.each(data, function(key, value){
			inputs += createInput(key, value);
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};