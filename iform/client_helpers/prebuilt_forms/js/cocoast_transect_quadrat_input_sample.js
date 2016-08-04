/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @author  Indicia Team
 * @license http://www.gnu.org/licenses/gpl.html GPL 3.0
 */

(function ($) {
  $(document).ready(function() {

  	// If subsample id is filled in -> entire row is editable.
  	// If any data is filled in on a row -> entire row is editable
  	// If any data on a level 1 row is filled in, the next level 1 row is editable
  	// If any data on a level 2 row is filled in, the next level 2 row is editable
    var setEditStatus = function (me) {
    	// called by an element in the row changeing:
    	// No matter what, this row is still editable: leave its state unchanged, apart from the copydown dat.
    	var parts = $(me).attr('name').split(':'); // Grid:Row<rowNumber>:[subsampleID]:
    	var subsampleID = parts[2];
    	var myRow = $(me).closest('tr');
    	var nextLevel1 = myRow.nextAll('.level1').first();
    	var nextLevel2 = myRow.next('.level2');
    	
    	// Make a note if any non-ignore/non-copydown data filled in on this row - myRowData = true/false.
    	var myRowData = (myRow.find('input,select').not('.ignore,.copydown').not('[value=]').length > 0);
    	var nextRowData;
    	if(subsampleID != '' || myRowData)
    		myRow.find('.copydown').removeAttr('disabled');
    	else
    		myRow.find('.copydown').attr('disabled','disabled');
    	
    	if (myRow.hasClass('level1') && nextLevel1.length > 0) {
    		nextRowData = (nextLevel1.find('input,select').not('.ignore').not('[value=]').length > 0); // Level1s do not have copydown data.	
        	parts = nextLevel1.find('input,select').first().attr('name').split(':'); // Grid:Row<rowNumber>:[subsampleID]:
        	subsampleID = parts[2];
        	if(subsampleID == '') {
    			if(nextRowData || myRowData)
    				nextLevel1.find('input,select').removeAttr('disabled');
    			else
    				nextLevel1.find('input,select').attr('disabled','disabled');
        	}
    	}
    	
    	if (nextLevel2.length > 0) {
    		nextRowData = (nextLevel2.find('input,select').not('.ignore,.copydown').not('[value=]').length > 0);   		
        	parts = nextLevel2.find('input,select').first().attr('name').split(':'); // Grid:Row<rowNumber>:[subsampleID]:
        	subsampleID = parts[2];
        	if(subsampleID == '') {
        		if(nextRowData)
        			nextLevel2.find('.copydown').removeAttr('disabled');
        		else 
        			nextLevel2.find('.copydown').attr('disabled','disabled');
    			if(nextRowData || myRowData)
    				nextLevel2.find('input,select').not('.copydown').removeAttr('disabled');
    			else
    				nextLevel2.find('input,select').not('.copydown').attr('disabled','disabled');
        	}
    	}
    };

	$( 'table#transect-input1').find('input,select').change(function() {
		// enforce extra enabling
		setEditStatus(this);
		// force all non ignored data on the species list to mandatory, if any of the
		// data is filled in on that row.
		var row = $(this).closest('tr');
		var filledIn = row.find('input,select').filter('[value!=]').not('.ignore').length;
		// ensure that the copydowns are set accordingly.
		if(row.hasClass('level1')){
	    	var level2s = row.nextUntil('.level1');
			filledIn += level2s.find('input,select').not('.ignore,.copydown').filter('[value!=]').not('.ignore').length;
		}
		if(filledIn == 1 && !row.hasClass('hasHadData') && indiciaData.fill_zeroes) { // only do this for first field filled in on a empty row
			row.find('input[value=]').not('.ignore').val(0);
		}
		if(filledIn > 0) {
			row.find('input,select').not('.ignore').addClass('required');
			row.addClass('hasHadData');
		} else {
			row.removeClass('hasHadData');
			row.find('input,select').not('.ignore').removeClass('required');
			row.find('p.inline-error').remove();
			row.find('.ui-state-error').removeClass('ui-state-error');
		}
		
		// If this is a level2, ensure that the copydowns are set accordingly.
		if(row.hasClass('level2')){
	    	var lastLevel1 = row.prevUntil(null,'.level1').first();
	    	lastLevel1.find('input,select').not('.ignore').first().change();
		}
	});
	
	$( 'table#transect-input1').find('.lvl1Master').change(function() {
		// store value in mirror fields
		var classes = $(this).attr('class').split(' ');
		for(var i = 0; i<classes.length; i++)
			if(classes[i].slice(0,5) == 'lvl1-')
				$('.'+classes[i]).not('.lvl1Master').val($(this).val());
	});

	// ensure mandatory set up initially, by doing above check on one element on each row.
	$( 'table#transect-input1 .first' ).change();
	$( 'table#transect-input1').find('.lvl1Master').change();
	
	$( 'table#transect-input1 .clear-row').click(function(e) {
	  var row = $(e.target.parentNode);
	  e.preventDefault();
	  if(!confirm('Are you sure you want to clear this row?')) return;
	  row.find('input,select').not('.ignore').val('');
	  row.find('.first').change();
	  // @todo unbind all event handlers
	  // TODO consider if photos attached to row.
	});

	updateImageCaptions = function() {
		  var i,j,k,m, option, attrs, attrReplace = {};
		  $('#captions').empty();
		  // 
		  // captions for the site image: {name} = location name, {diff} = differentiator
		  for(i=0; i<indiciaData.site_caption_differentiator.length; i++){
		    $('#captions').append('<option value="'+
				indiciaData.site_caption_template.replace('{name}',$('#sample\\:location_name').val())
					.replace('{nameNS}',$('#sample\\:location_name').val().replace(' ',''))
					.replace('{diff}', indiciaData.site_caption_differentiator[i])
				+'">');
		  }
		  attrs = Object.keys(indiciaData.quadrat_caption_attribute_mapping);
		  for(i=0; i<attrs.length; i++) {
			  // we make the assumption that these are radio buttons.
			  var val = $('input[id=^smpAttr\\:'+attrs[i].substring(4)+'\\:]:checked').val();
			  for(j=0; j<indiciaData.quadrat_caption_attribute_mapping[attrs[i]].length; j++) {
				  if(val == indiciaData.quadrat_caption_attribute_mapping[attrs[i]][j][0])
					  val = indiciaData.quadrat_caption_attribute_mapping[attrs[i]][j][1];
			  }
			  attrReplace[attrs[i]] = val;
		  }
		  // captions for quadrat images: {name} = location name, {N1} = level 1 value, {caption1} = level 1 attribute caption, {N2} = level 2 value, {caption2} = level 2 attribute caption, {diff} = differentiator, and {attr<x>} = value of sample attribute <x> - subject to a possible mapping
		  for(i=0; i < indiciaData.limit1; i++){
		  	for(j=0; j < indiciaData.limit2; j++){
		  	  for(k=0; k < indiciaData.quadrat_caption_differentiator.length; k++){
				option = indiciaData.quadrat_caption_template.replace('{name}',$('#sample\\:location_name').val())
					.replace('{nameNS}',$('#sample\\:location_name').val().replace(' ',''))
					.replace('{N1}', i+1)
					.replace('{N2}', j+1)
					.replace('{diff}', indiciaData.quadrat_caption_differentiator[k]);
				for(m=0; m<attrs.length; m++) {
				  option = option.replace('{'+attrs[m]+'}', attrReplace[attrs[m]]);
				}
				$('#captions').append('<option value="'+option+'">');
			  }
			}
		  }
		};
	$('#sample\\:location_name').change(updateImageCaptions);
	if(typeof indiciaData.quadrat_caption_attribute_mapping !== 'undefined') {
		attrs = Object.keys(indiciaData.quadrat_caption_attribute_mapping);
		for(i=0; i<attrs.length; i++) {
			// we make the assumption that these are radio buttons.
			$('input[type=radio][id=^smpAttr\\:'+attrs[i].substring(4)+'\\:]').change(updateImageCaptions);
		}
	}
	$('#sample\\:location_name').change();

  });
})(jQuery);
