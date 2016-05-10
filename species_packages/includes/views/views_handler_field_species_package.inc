<?php

/**
 * @file
 * Definition of views_handler_field_variable.
 */

/**
 * A handler to provide a field that display system variables.
 *
 * @ingroup views_field_handlers
 */
class views_handler_field_species_package extends views_handler_field {

  /**
   * Called to add the field to a query.
   */
  function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * Render the field.
   *
   * @param stdClass $values
   *   The values retrieved from the database.
   */
  function render($values) {
  	$none = t('None');
  	
  	$data = variable_get('species_packages_'.($values->uid), false);
  	if($data === false || $data == '') return $none;

  	$data = json_decode($data);
  	if(!is_array($data)) return $none;

  	$packageList = array();
  	foreach($data as $package) {
  		// ignore survey_setting
  		$package = explode(':',$package);
  		$packageList[] = species_packages_get_name($package[1]);
  	}
  	return implode(',',$packageList);
  	 
  }
}
