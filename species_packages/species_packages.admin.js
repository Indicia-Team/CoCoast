var species_packages_data = {newSpeciesCounter: 1};

(function ($) {
  $(document).ready(function() {
	$('label').addClass('option'); // shrinks labels onto same line as field.
	$( "body" ).delegate( 'input[name^="edit-add-species-"]:hidden', "change", function() {
		// Callback from autocomplete. Add an extra species entry to the list.
		if($(this).val() == '') return;
		var truncatedName = $(this).attr('name');
		var packageID = truncatedName.slice(17); //
		var val = $(this).val();
		var display = $('input[name="'+truncatedName+'\:taxon"]').val();
		if($('input[name="edit-package-'+packageID+'[]"][value="'+val+'"]').length == 0)
			$('#edit-package-'+packageID).append(' <span class="edit-package-list-entry">' + 
									'<span class="ui-icon ui-icon-arrow-1-w species-packages-move-left" title="Click here to move the '+display+' entry to the left"></span>' +
									display +
									'<input type="hidden" name="edit-package-'+packageID+'[]" value="' + val + '"/>' +
									'<span class="species-packages-delete-species" title="Click here to delete the '+display+' entry"></span>' +
									'<span class="ui-icon ui-icon-arrow-1-e species-packages-move-right" title="Click here to move the '+display+' entry to the right"></span>' +
									'</span>');
		$('#edit-package-'+packageID).find('.ui-icon-arrow-1-w,.ui-icon-arrow-1-e').show();
		$('#edit-package-'+packageID).find('.ui-icon-arrow-1-w:first,.ui-icon-arrow-1-e:last').hide();
		$(this).val('');
		$('input[name="'+truncatedName+'\:taxon"]').val('');
	});
	$( "body" ).delegate( ".species-packages-delete-species", "click", function() {
		 $(this).parent('span').remove();
	});
	$( "body" ).delegate( ".species-packages-move-left", "click", function() {
		var thisEntry = $(this).parent('span');
		var previous = thisEntry.prev();
		if(previous.length > 0) previous.insertAfter(thisEntry);
		var list = $(this).closest( '[id^=edit-package-]' );
		list.find('.ui-icon-arrow-1-w,.ui-icon-arrow-1-e').show();
		list.find('.ui-icon-arrow-1-w:first,.ui-icon-arrow-1-e:last').hide();
	});
	$( "body" ).delegate( ".species-packages-move-right", "click", function() {
		var thisEntry = $(this).parent('span');
		var next = thisEntry.next();
		if(next.length > 0) next.insertBefore(thisEntry);
		var list = $(this).closest( '[id^=edit-package-]' );
		list.find('.ui-icon-arrow-1-w,.ui-icon-arrow-1-e').show();
		list.find('.ui-icon-arrow-1-w:first,.ui-icon-arrow-1-e:last').hide();
	});
	// Initialisation: hide arrows at end of the lists.
	$( '[id^=edit-package-]' ).each(function(idx, elem) {
		$(elem).find('.ui-icon-arrow-1-w:first,.ui-icon-arrow-1-e:last').hide();
	});
	$( "body" ).delegate( '[id^="button-add-new-species-package-"]', "click", function() {
		// Add a new Species Package.
		// TODO needs to be built from theme in php, then made available.
		var truncatedName = $(this).attr('id');
		var surveyID = truncatedName.slice(31); //
		var tag = 'new-'+species_packages_data.newSpeciesCounter;
		$(this).before(
			'<fieldset id="edit-'+tag+'" class="form-wrapper">' +
			  '<legend><span class="fieldset-legend">New Species Package '+species_packages_data.newSpeciesCounter+'</span></legend>' +
			  '<div class="fieldset-wrapper">' +
			    '<input type="hidden" value="'+tag+'" name="species-package[]">' +
			    '<input type="hidden" value="'+surveyID+'" name="species-package-survey-'+tag+'">' +
			    '<div class="form-item form-type-textfield form-item-newSpecies">' +
			      '<label for="edit-package-package-name-'+tag+'" class="option">Species Package Name</label>' +
			      '<input type="text" class="form-text" maxlength="128" size="60" value="New Species Package" name="species-package-name-'+tag+'" id="edit-package-package-name-'+tag+'">' +
			    '</div>' +
			    '<div class="description">You must perform a Save after adding a species package, before you can add species entries to it.</div>' +
			  '</div>' +
			'</fieldset>');
		species_packages_data.newSpeciesCounter++;
	});
  });
})(jQuery);