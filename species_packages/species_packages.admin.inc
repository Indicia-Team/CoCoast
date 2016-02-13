<?php
/**
* @file
* Include file to define the admin interface for the iform module.
*/

// TODO extend to allow switching package and species inactive.

/**
 * A form definition for providing global configuration options for the Species Package submodule.
 * Used both in the installation profile as an install task, and provided on the admin menu.
 */
function species_packages_settings_form($form, &$form_state, $url='', $submit_handler='') {
	$form['active'] = array(
			'#type' => 'checkbox',
			'#title' => t('Selection in user accounts active'),
			'#default_value' => variable_get('species_packages_setting_active'),
			'#description' => 'Ticked if the ability to set the species packages within the user accounts is active. This allows the species packages to be set up before being made available to the users.'
	);
	$form['trained-role'] = array(
			'#type' => 'textfield',
			'#title' => t('Trained Role'),
			'#title_display' => 'before',
			'#default_value' => variable_get('species_packages_setting_training_role'),
			'#description' => 'The role assigned to a user to indicate that they have received training.'
	);
	$form['taxon-lists'] = array(
			'#type' => 'textfield',
			'#title' => t('Taxon Lists'),
			'#title_display' => 'before',
			'#default_value' => variable_get('species_packages_setting_taxon_lists'),
			'#description' => 'A comma separated list of Indicia Taxon List IDs, detailing which lists should be used when looking up the species to be included in the Species Packages.'
	);
	$form['continue'] = array(
			'#type' => 'submit',
			'#value' => t('Save'),
			'#validate' => array('species_packages_settings_form_validate'),
	);

	// Note that #action is set to the url passed through from
	// installer, ensuring that it points to the same page, and
	// #redirect is FALSE to avoid broken installer workflow.
	$form['errors'] = array();
	if (!empty($url)) {
		$form['#action'] = $url;
		$form['#redirect'] = FALSE;
	}
	if (empty($submit_handler))
		$form['#submit'][] = 'species_packages_settings_form_submit';
	else
		$form['#submit'][] = $submit_handler;

	return $form;
}

/**
 * Implement hook_validate.
 * Checks the Species Package submodule configuration settings are valid.
 * @param array $form
 * @param array $form_state
 */
function species_packages_settings_form_validate($form, $form_state) {
}

/**
 * Implement hook_submit.
 * Submit the Species Package submodule configuration settings.
 * @param array $form
 * @param array $form_state
 */
function species_packages_settings_form_submit($form, &$form_state) {
	variable_set('species_packages_setting_training_role', $form_state["values"]["trained-role"]);
	variable_set('species_packages_setting_taxon_lists', $form_state["values"]["taxon-lists"]);
	variable_set('species_packages_setting_active', $form_state["values"]["active"]);
	drupal_set_message(t('Species Package settings saved.'));
}

/** 
 * A form definition for providing species Package mappings for the Species Package submodule.
 * Used both in the installation profile as an install task, and provided on the admin menu.
 */
function species_packages_package_form($form, &$form_state, $url='', $submit_handler='') {
  iform_load_helpers(array('data_entry_helper'));
  $readAuth = data_entry_helper::get_read_auth(variable_get('indicia_website_id', 0), variable_get('indicia_password', ''));
  $rows = data_entry_helper::get_population_data(array(
  		'table' => 'survey',
  		'extraParams' => $readAuth + array('website_id' => variable_get('indicia_website_id', '')),
		'nocache' => true
  ));
  foreach ($rows as $row) {
    $occAttrs = data_entry_helper::get_population_data(array(
  		'table' => 'occurrence_attribute',
  		'extraParams' => $readAuth + array('restrict_to_survey_id' => $row['id']),
		'nocache' => true
    ));
  	$form['survey:'.$row['id']] = array(
  			'#type' => 'fieldset',
  			'#title' => $row['title']." (Indicia Survey ID ".$row['id'].")"
  	);
  	$results = db_query('SELECT sid, package_name FROM {species_packages} WHERE indicia_survey_id = :isid ORDER BY sid', array(':isid' => $row['id']));
    foreach ($results as $result) {
  		$set = (string)($result->sid);
	  	$form['survey:'.$row['id']][$set] = array(
  			'#type' => 'fieldset',
  			'#title' => 'Species Package &quot;'.$result->package_name."&quot; (".$set.")"
	  	);
    	$form['survey:'.$row['id']][$set]['species-package-'.$set] = array(
    		'#name' => 'species-package[]',
  			'#type' => 'hidden',
  			'#value' => $set
	  	);
    	$form['survey:'.$row['id']][$set]['species-package-survey-'.$set] = array(
  			'#type' => 'hidden',
  			'#value' => $row['id']
  		);
    	$form['survey:'.$row['id']][$set]['species-package-name-'.$set] = array(
  			'#type' => 'textfield',
  			'#title' => t('Species Package Name'),
    		'#title_display' => 'before',
  			'#default_value' => $result->package_name
  		);
/*  	$form['survey:'.$row['id']][(string)$set]['1toggle_me'] = array(
  			'#type' => 'checkbox',
  			'#title' => t('Is this package active?'),
  	); */
  		$form['survey:'.$row['id']][$set]['3description'] = array(
  			'#markup' => _species_packages_build_species_markup($set, $occAttrs)
  		);
    	$form['survey:'.$row['id']][$set]['4newSpecies'] = array(
  			'#markup' => data_entry_helper::autocomplete(array(
  					'label' => t('Add Species to Package'),
  					'labelClass' => 'option',
         			'fieldname' => 'edit-add-species-'.$set,
         			'table' => 'taxa_taxon_list',
         			'captionField' => 'taxon',
         			'valueField' => 'id',
         			'extraParams' => $readAuth + array('preferred'=>'t',
         					'view' => 'cache',
         					'query' => json_encode(array('in' => array('taxon_list_id' => explode(',', variable_get('species_packages_setting_taxon_lists')))))),
     				)).'<div class="description">You must perform a Save after adding a Species to the Package, before you can assign an attribute. The search is done using the preferred taxon name, though the common name will be used in the input form.</div>');
    }
    // don't want a submit button, so have to work around, based on theme_button.
    $element = array(
    		'#id'=>'button-add-new-species-package-'.$row['id'],
  			'#type' => 'button',
  			'#value' => t('Add new Species Package in ').$row['title'],
  	);
    element_set_attributes($element, array('id', 'name', 'value', 'type'));
    $element['#attributes']['class'][] = 'form-submit';
    /*	if (!empty($element['#attributes']['disabled'])) {
    		$element['#attributes']['class'][] = 'form-button-disabled';
    		} */
  	$form['survey:'.$row['id']]['new'] = array(
  			'#markup' => '<input' . drupal_attributes($element['#attributes']) . ' />'
  	);
  }
  if(count($rows) == 0) {
  	$form['nothing'] = array(
  			'#markup' => '<p>There are no surveys currently assigned to the Indicia Website record.</p>'
  	);
  }
  $form['continue'] = array(
    	'#type' => 'submit', 
	    '#value' => t('Save'),
	    '#validate' => array('species_packages_package_form_validate'),
  );

  // Note that #action is set to the url passed through from
  // installer, ensuring that it points to the same page, and
  // #redirect is FALSE to avoid broken installer workflow.
  $form['errors'] = array();
  if (!empty($url)) {
    $form['#action'] = $url;
    $form['#redirect'] = FALSE;
  }
  if (empty($submit_handler))
    $form['#submit'][] = 'species_packages_package_form_submit';
  else
    $form['#submit'][] = $submit_handler;
  
  $form['#attached'] = iform_get_form_resource_attachments();
  $form['#attached']['js'][] = array('data'=>drupal_get_path('module', 'species_packages').'/species_packages.admin.js');
  drupal_add_library('system', 'ui', TRUE);
  drupal_add_css(drupal_get_path('module', 'species_packages').'/species_packages.admin.css');
  
  return $form;
}

/**
 * Function to build the HTML to represent the list of species entries in a package
 * @param integer $sid The Species Package identifier.
 * @param Array $occAttrs List of occurrence attributes that can be assign to each Species entry.
 * @return string
 */
function _species_packages_build_species_markup($sid, $occAttrs) {
  	$results = db_query('SELECT indicia_taxa_taxon_list_id, indicia_occurrence_attribute_id, taxon, common FROM {species_packages_species} WHERE species_package_sid = :islid ORDER BY roworder', array(':islid' => $sid));
	$r = '<p>Contents of species package : <span id="edit-package-'.$sid.'">';
	foreach ($results as $result) {
		$attrOpts = array();
		foreach($occAttrs as $occAttr)
			$attrOpts[] = '<option value="'.$occAttr['id'].'"'.
				($result->indicia_occurrence_attribute_id == $occAttr['id'] ? ' selected="selected"' : '').
				'>'.$occAttr['caption'].'</option>';
		$r .= ' <span class="edit-package-list-entry" >' .
				'<span class="ui-icon ui-icon-arrow-1-w species-packages-move-left" title="Click here to move the ' . $result->common . ' entry to the left"></span>'.
				$result->common . ' (<i>' . $result->taxon . '</i>) ' .
				'<input type="hidden" name="edit-package-'.$sid.'[]" value="' . $result->indicia_taxa_taxon_list_id . '" />' .
				(count($attrOpts) == 1 ? '<input type="hidden" name="attr-species-'.$sid.'-'. $result->indicia_taxa_taxon_list_id .'" value="' . $occAttrs[0]['id'] . '" />' :
						'<select name="attr-species-'.$sid.'-'. $result->indicia_taxa_taxon_list_id .'">'.implode('',$attrOpts).'</select>').
				'<span class="species-packages-delete-species" title="Click here to delete the ' . $result->common . ' entry"></span>'.
				'<span class="ui-icon ui-icon-arrow-1-e species-packages-move-right" title="Click here to move the ' . $result->common . ' entry to the right"></span>'.
				'</span>';
	}
	$r .= '</span></p>';
	return $r;
}

/** 
 * Implement hook_validate.
 * Checks the Species Package mappings are valid.
 * @param array $form 		
 * @param array $form_state
 */
function species_packages_package_form_validate($form, $form_state) {
	// Possible checks?
	// Copy inputs to values?
}

/**
 * Implement hook_submit.
 * Submit the Species Package mappings.
 * @param array $form
 * @param array $form_state
 */
function species_packages_package_form_submit($form, &$form_state) {
	// TODO move inputs to values.
	$readAuth = data_entry_helper::get_read_auth(variable_get('indicia_website_id', ''), variable_get('indicia_password', ''));
	foreach($form_state["input"]["species-package"] as $package){
		$surveyID = $form_state["input"]["species-package-survey-".$package];
		if(substr($package, 0, 4) == "new-") {
			db_insert('species_packages')
				->fields(array('package_name' => $form_state["input"]["species-package-name-".$package],
								'indicia_survey_id' => $surveyID,
								'deleted' => 0,
								'in_use' => 1)) // sid auto filled in
				->execute();
		} else {
			db_update('species_packages')
				->fields(array('package_name' => $form_state["input"]["species-package-name-".$package]))
				->condition('sid', $package, '=')
				->condition('indicia_survey_id', $surveyID, '=')
				->execute();
			db_delete('species_packages_species')
				->condition('species_package_sid', $package, "=")
				->execute();
		}
		if(isset($form_state["input"]["edit-package-".$package])) {
			$taxa = data_entry_helper::get_population_data(array(
					'table' => 'taxa_taxon_list',
					'extraParams' => array_merge($readAuth, array('view'=> 'cache', 'id' => $form_state["input"]["edit-package-".$package]))
			));
			$rowNumber = 1;
			foreach($form_state["input"]["edit-package-".$package] as $species) { // keep form order
				$attr = isset($form_state["input"]["attr-species-".$package.'-'.$species]) 
							? $form_state["input"]["attr-species-".$package.'-'.$species]
							: 0;
				foreach($taxa as $taxon) {
					if($species == $taxon['id'])
						db_insert('species_packages_species')
							->fields(array('species_package_sid' => $package,
											'roworder' => $rowNumber,
											'indicia_taxa_taxon_list_id' => $species,
											'indicia_occurrence_attribute_id' => $attr,
											'taxon' => $taxon['taxon'],
											'common' => $taxon['default_common_name']))
							->execute();
						
				}
				$rowNumber++;
			}
		}
	}
	drupal_set_message(t('Species Packages mappings saved.'));
}

