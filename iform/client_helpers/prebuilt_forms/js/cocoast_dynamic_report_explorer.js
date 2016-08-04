var hook_reportFilters_alter_paneObj;

(function ($) {

	hook_reportFilters_alter_paneObj = function(paneObj) {
		paneObj.who = {
			// loadFilter converts generic data in definition to the filter definition. Not required in this case.
	        getDescription:function() {
	          // My records takes priority
	          if (indiciaData.filter.def.my_records) {
	            return indiciaData.lang.MyRecords;
	          } else {
	        	var description = [];
	        	$.each(indiciaData.hubList, function(idx, hub){
	        		if(typeof indiciaData.filter.def['hub'+hub[0]] != "undefined" && indiciaData.filter.def['hub'+hub[0]])
	        			description.push(hub[1]);
	        	});
	        	if(description.length>0)
	        		return "Member of hub: "+description.join(', '); // TODO i18n
	        	else
	        		return '';
	          }
	        },
	        applyFormToDefinition: function(){ // convert the form fields to the definition of the filter.
	        	// This is on top of storing the fields themselves
	        	// in this case we generate the list of users indicia ids
	        	// Only concern is this list may change on a stored filter.
	        	// TODO my_records takes priority.
	        	var user_id_list = [];
	        	delete indiciaData.filter.def.user_id_list;
	        	$.each(indiciaData.hubList, function(idx, hub){
	        		if(typeof indiciaData.filter.def['hub'+hub[0]] != "undefined" && 
	        				indiciaData.filter.def["hub"+hub[0]] &&
	        				indiciaData["hub"+hub[0]] != '')
	        			user_id_list = user_id_list.concat(indiciaData["hub"+hub[0]].split(','));
	        	});
				if(user_id_list.length>0)
					indiciaData.filter.def.user_id_list = user_id_list.join(',');
	        },
	        refreshFilter: function() {
	        	this.applyFormToDefinition(); // reload the current hub user list
	        },
	        // preloadForm not required for this filter.
	        loadForm:function(context) {
	          // copy the filter definition to the form.
	          // The setting of the control values themselves is already handled.
	          var show_button = false;
	          var show_instructions = false;
		      $('#controls-filter_who button').hide();
	          $('#controls-filter_who .context-instruct').hide();
	          if (context && context.my_records) {
	            $('#my_records').attr('disabled', true);
	            show_instructions = true;
	          } else {
	            $('#my_records').removeAttr('disabled');
	        	show_button = true;
	          }
	          $.each(indiciaData.hubList, function(idx, hub){
	        	if (context && context['hub'+hub[0]]) {
	  	            $('#hub'+hub[0]).attr('disabled', true);
		            show_instructions = true;
	  	        } else {
	  	            $('#hub'+hub[0]).removeAttr('disabled');
		        	show_button = true;
	  	        }
	          });
	          if(show_button)
		          $('#controls-filter_who button').show();
	          if(show_instructions)
		          $('#controls-filter_who .context-instruct').show();

	        }
	        // getDefaults:function() {} // what does this do?
	      };

		return paneObj;
	}

}(jQuery));