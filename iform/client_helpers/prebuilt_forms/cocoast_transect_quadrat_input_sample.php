<?php
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

require_once 'includes/map.php';
require_once 'includes/user.php';
require_once 'includes/language_utils.php';
require_once 'includes/form_generation.php';

/**
 * A form for data entry of transect data by entering counts of each for quadrats on the transect.
 * This form has been created specifically for Capturing Our Coast.
 * Use of this form in other situations runs the risk of the form being changed without notice to meet
 * the requirements of Capturing Our Coast.
 * 
 * This form should be copied into the client_helpers/prebuilt_forms directory within the iform module
 * on the Capturing Our Coast website.
 */
class iform_cocoast_transect_quadrat_input_sample {

  private static $sampleId;
	
  /**
   * Return the form metadata. Note the title of this method includes the name of the form file. This ensures
   * that if inheritance is used in the forms, subclassed forms don't return their parent's form definition.
   * @return array The definition of the form.
   */
  public static function get_cocoast_transect_quadrat_input_sample_definition() {
    return array(
      'title'=>'Capturing Our Coast Transect Quadrat Sample Input (1)',
      'category' => 'Forms for specific surveying methods',
      'description'=>'A form for data entry of transect data by entering counts of each for quadrats on the transect. '.
					'This form has been created specifically for Capturing Our Coast. '.
					'Use of this form in other situations runs the risk of the form being changed without notice to meet '.
    				'the requirements of Capturing Our Coast.'
    );
  }

  /**
   * Get the list of parameters for this form.
   * @return array List of parameters that this form requires.
   */
  public static function get_parameters() {
    return array_merge(
      iform_map_get_map_parameters(),
      iform_map_get_georef_parameters(),
      array(
        array(
          'name'=>'survey_id',
          'caption'=>'Survey',
          'description'=>'The survey that data will be posted into.',
          'type'=>'select',
          'table'=>'survey',
          'captionField'=>'title',
          'valueField'=>'id',
          'group'=>'General Form Settings',
          'siteSpecific'=>true
        ),
        array(
          'name'=>'sample_tab_label',
          'caption'=>'Sample Tab Title',
          'description'=>'The title to be used on the main sample tab.',
          'type'=>'string',
          'required' => true,
          'siteSpecific'=>true,
          'group'=>'General Form Settings'
        ),
        array(
          'name'=>'occurrence_tab_label',
          'caption'=>'Species Tab Title',
          'description'=>'The title to be used on the species checklist tab.',
          'type'=>'string',
          'required' => true,
          'group'=>'General Form Settings',
          'siteSpecific'=>true
        ),
        array(
          'name'=>'media_tab_label',
          'caption'=>'Media Tab Title',
          'description'=>'The title to be used on the media tab.',
          'type'=>'string',
          'required' => true,
          'group'=>'General Form Settings',
          'siteSpecific'=>true
        ),
      	array(
          'name'=>'transect_level_sample_method_id',
          'caption'=>'Transect Level Sample Method',
          'description'=>'Select the term used for transect level sample method.',
          'type' => 'select',
          'table' => 'termlists_term',
          'captionField' => 'term',
          'valueField' => 'id',
          'extraParams' => array('termlist_external_key'=>'indicia:sample_methods'),
          'required' => true,
          'group'=>'General Form Settings',
          'siteSpecific'=>true
        ),
        array(
          'name'=>'quadrat_level_sample_method_id',
          'caption'=>'Quadrat Level Sample Method',
          'description'=>'Select the term used for Quadrat level sample method.',
          'type' => 'select',
          'table'=>'termlists_term',
          'captionField'=>'term',
          'valueField'=>'id',
          'extraParams' => array('termlist_external_key'=>'indicia:sample_methods'),
          'required' => true,            
          'group'=>'General Form Settings',
          'siteSpecific'=>true
        ), 
        array(
          'name'=>'spatial_systems',
          'caption'=>'Allowed Spatial Ref Systems',
          'description'=>'List of allowable spatial reference systems, comma separated. Use the spatial ref system code (e.g. OSGB or the EPSG code number such as 4326). '.
              'Set to "default" to use the settings defined in the IForm Settings page.',
          'type'=>'string',
          'default' => 'default',
          'group'=>'Other Map Settings',
          'siteSpecific'=>true
        ),
        array(
          'name'=>'defaults',
          'caption'=>'Default Values',
          'description'=>'Supply default values for each field as required. On each line, enter fieldname=value. For custom attributes, '.
              'the fieldname is the untranslated caption. For other fields, it is the model and fieldname, e.g. occurrence.record_status. '.
              'For date fields, use today to dynamically default to today\'s date. NOTE, currently only supports occurrence:record_status and '.
              'sample:date but will be extended in future.',
              'type'=>'textarea',
              'default'=>'occurrence:record_status=C',
          'group'=>'General Form Settings',
          'required' => false,
          'siteSpecific'=>true
        ),
        array(
          'name'=>'custom_attribute_options',
          'caption'=>'Options for custom attributes',
          'description'=>'A list of additional options to pass through to custom attributes, one per line. Each option should be specified as '.
              'the attribute name followed by | then the option name, followed by = then the value. For example, smpAttr:1|class=control-width-5.',
          'type'=>'textarea',
          'group'=>'General Form Settings',
          'required'=>false,
          'siteSpecific'=>true
        ),
      	array(
          'name'=>'sample_attribute_id_1',
          'caption'=>'First Sample Attribute',
          'description'=>'The attribute used as the primary descriptor for the samples in the Species Grid',
          'type'=>'select',
          'table'=>'sample_attribute',
          'extraParams' => array('data_type'=>'I'),
      	  'captionField'=>'caption',
          'valueField'=>'id',
          'required' => true,
          'group'=>'Species Grid',
          'siteSpecific'=>true
        ),
      	array(
          'name'=>'sample_attribute_id_1_limit',
          'caption'=>'First Sample Attribute Limit',
          'description'=>'The normal top value (extended only to accommodate existing any data) for the first sample attribute in the Species Grid. ',
          'type'=>'int',
          'required' => true,
      	  'default' => 6,
          'group'=>'Species Grid',
          'siteSpecific'=>true
      	),
      	array(
          'name'=>'sample_attribute_id_2',
          'caption'=>'Second Sample Attribute',
          'description'=>'The attribute used as the optional secondary descriptor for the samples in the Species Grid',
          'type'=>'select',
          'table'=>'sample_attribute',
          'extraParams' => array('data_type'=>'I'),
      	  'captionField'=>'caption',
          'valueField'=>'id',
          'required' => false,
          'group'=>'Species Grid',
          'siteSpecific'=>true
        ),
      	array(
          'name'=>'sample_attribute_id_2_limit',
          'caption'=>'Second Sample Attribute Limit',
          'description'=>'The normal top value (extended only to accommodate existing any data) for the secondary sample attribute in the Species Grid. ',
          'type'=>'int',
          'required' => false,
          'group'=>'Species Grid',
          'siteSpecific'=>true
    	),
        array(
          'name'=>'level_1_attributes',
          'caption'=>'Level 1 attributes',
          'description'=>'A comma separated list of sample attribute IDs which have a single entry per Level 1.',
          'type'=>'text_input',
          'required' => false,
          'group'=>'Species Grid',
          'siteSpecific'=>true
        ),
      	array(
          'name'=>'taxon_headings_extras',
          'caption'=>'Taxon Headings Extras',
          'description'=>'A comma separated list on &quot;&lt;Attribute ID&gt;:&lt;Text&gt;&quot;, where &lt;Text&gt is added to the end of the taxon name in the Species Grid heading. This is used to give clarity over what the input attribute is for that taxon.',
          'type'=>'text_input',
          'required' => false,
          'group'=>'Species Grid',
          'siteSpecific'=>true
        )
      )
    );
  }

  /**
   * Return the generated form output.
   * @param array $args List of parameter values passed through to the form depending on how the form has been configured.
   *                    This array always contains a value for language.
   * @param integer $nid The Drupal node object's ID.
   * @param array $response When this form is reloading after saving a submission, contains the response from the service call.
   *                        Note this does not apply when redirecting (in this case the details of the saved object are in the $_GET data).
   * @return Form HTML.
   */
  public static function get_form($args, $nid, $response=null) {
  	global $user;

//    if (!function_exists('module_exists') || !module_exists('iform_ajaxproxy'))
//      return 'This form must be used in Drupal with the Indicia AJAX Proxy module enabled.';
    if (!function_exists('module_exists') || !module_exists('easy_login'))
      return 'This form must be used in Drupal with the Easy Login module enabled.';
    if (!function_exists('module_exists') || !module_exists('species_packages'))
    	return 'This form must be used in Drupal with the Indicia species_packages module enabled.';
    
    $r = "";
    if (isset($response['error']))
      $r .= data_entry_helper::dump_errors($response);
    iform_load_helpers(array('map_helper','report_helper'));
    $auth = data_entry_helper::get_read_write_auth($args['website_id'], $args['password']);

    self::$sampleId = false;
    if (isset(data_entry_helper::$entity_to_load['sample:id']) && data_entry_helper::$entity_to_load['sample:id'] != "") {
    	// have just posted a (failed) edit to the existing parent sample, so can use it to get the parent location id.
    	self::$sampleId = data_entry_helper::$entity_to_load['sample:id'];
    } else {
    	if (isset($response['outer_id']))
    		// have just posted a new parent sample, so can use it to get the parent location id.
    		self::$sampleId = $response['outer_id'];
    	else {
    		self::$sampleId = isset($_GET['sample_id']) ? $_GET['sample_id'] : null;
    		$existing=true;
    	}
    }

    if (self::$sampleId) {
    	data_entry_helper::load_existing_record($auth['read'], 'sample', self::$sampleId, 'detail', false, true);
    	$args['survey_id'] = data_entry_helper::$entity_to_load['sample:survey_id'];
    } else if(!isset(data_entry_helper::$entity_to_load['sample:id']) &&
    		isset($_GET['survey_id']) && $_GET['survey_id'] != "") {
    	$args['survey_id'] = $_GET['survey_id'];
    }
    // overkill here with checks for else case, but gets the survey title.
    if(!is_int($args['survey_id']) && (!is_string($args['survey_id']) || strval(intval($args['survey_id'])) != $args['survey_id']))
    	return "FATAL ERROR: Supplied survey_id value (".$args['survey_id'].") is not an integer.<br/>";
    $survey = data_entry_helper::get_population_data(array(
    	'table' => 'survey',
    	'extraParams' => $auth['read'] + array('view'=>'detail','id'=>$args['survey_id'],'website_id'=>$args['website_id'])
    ));
    if(count($survey) != 1)
    	return "FATAL ERROR: Supplied survey_id value (".$args['survey_id'].") is not valid survey for this website.<br/>";
    $surveyTitle = $survey[0]['title'];

    if(($package = species_packages_get_species($user->uid, $args['survey_id'])) === false)
    	return "FATAL ERROR: You (User ID ".($user->uid).") have not selected a Species Package for survey &quot;".$surveyTitle."&quot; (Survey ID ".($args['survey_id']).") in your Account settings.<br/>";
    if(count($package) === 0)
    	return "FATAL ERROR: The species package for &quot;".$surveyTitle."&quot; in your Account settings has no taxa assigned to it.<br/>";
    
    if(isset(data_entry_helper::$entity_to_load['sample:id']))
        $r .= "<h2>".data_entry_helper::$entity_to_load['sample:location_name']." on ".data_entry_helper::$entity_to_load['sample:date']."</h2>\n";
    $r .= "<div id=\"tabs\">\n";
    $tabs = array();
    $smpTab = self::get_sample_tab($args, $nid, $auth);
    $mediaTab = self::get_media_tab($args, $nid, $auth);
    $occTab = self::get_occurrences_tab($args, $nid, $auth); // Note this messes with the templates, so done last
    $tabs['#sample'] = t($args['sample_tab_label']);
    if($occTab != "")
    	$tabs['#occurrences'] = t($args['occurrence_tab_label']);
    if($occTab != "")
    	$tabs['#media'] = t($args['media_tab_label']);
    $r .= data_entry_helper::tab_header(array('tabs'=>$tabs));
    data_entry_helper::enable_tabs(array('divId'=>'tabs','style'=>'Tabs'));
    $r .= $smpTab.$occTab.$mediaTab.'</div>';
    return $r;
  }

  /**
   * Return the generated output for the main sample tab.
   * @param array $args List of parameter values passed through to the form depending on how the form has been configured.
   *                    This array always contains a value for language.
   * @param integer $nid The Drupal node object's ID.
   * @param object $auth The full read-write authorisation.
   * @return HTML.
   */
  public static function get_sample_tab($args, $nid, $auth) {
    global $user;

    $attributes = data_entry_helper::getAttributes(array(
    		'id' => self::$sampleId,
    		'valuetable'=>'sample_attribute_value',
    		'attrtable'=>'sample_attribute',
    		'key'=>'sample_id',
    		'fieldprefix'=>'smpAttr',
    		'extraParams'=>$auth['read'],
    		'survey_id'=>$args['survey_id'],
    		'sample_method_id'=>$args['transect_level_sample_method_id']
    ));
    
    $r = '<form method="post" id="sample">';

    $r .= $auth['write'];
    // we pass through the read auth. This makes it possible for the get_submission method to authorise against the warehouse
    // without an additional (expensive) warehouse call, so it can get subsample details.
    $r .= '<input type="hidden" name="read_nonce" value="'.$auth['read']['nonce'].'"/>';
    $r .= '<input type="hidden" name="read_auth_token" value="'.$auth['read']['auth_token'].'"/>';
    
    $r .= '<input type="hidden" name="page" value="mainSample"/>';
    $r .= '<input type="hidden" name="website_id" value="'.$args['website_id'].'"/>';
    if (isset(data_entry_helper::$entity_to_load['sample:id'])) {
      $r .= '<input type="hidden" name="sample:id" value="'.data_entry_helper::$entity_to_load['sample:id'].'"/>';
    }
    $r .= '<input type="hidden" name="sample:survey_id" value="'.$args['survey_id'].'"/>';

    $r .= get_user_profile_hidden_inputs($attributes, $args, isset(data_entry_helper::$entity_to_load['sample:id']), $auth['read']);

    $r .= data_entry_helper::text_input(array(
    		'label' => lang::get('Location Name'),
    		'fieldname' => 'sample:location_name',
    		'class' => 'control-width-5'
    ));
    
    if (isset(data_entry_helper::$entity_to_load['sample:date']) && preg_match('/^(\d{4})/', data_entry_helper::$entity_to_load['sample:date'])) {
    	// Date has 4 digit year first (ISO style) - convert date to expected output format
    	// @todo The date format should be a global configurable option. It should also be applied to reloading of custom date attributes.
    	$d = new DateTime(data_entry_helper::$entity_to_load['sample:date']);
    	data_entry_helper::$entity_to_load['sample:date'] = $d->format('d/m/Y');
    }
    $r .= data_entry_helper::date_picker(array(
    		'label' => lang::get('Date'),
    		'fieldname' => 'sample:date',
    ));
    
    // are there any option overrides for the custom attributes?
    if (isset($args['custom_attribute_options']) && $args['custom_attribute_options'])
    	$blockOptions = get_attr_options_array_with_user_data($args['custom_attribute_options']);
    else
    	$blockOptions=array();
    $r .= get_attribute_html($attributes, $args, array('extraParams'=>$auth['read']), null, $blockOptions);
    
    $systems=array();
    $list = explode(',', str_replace(' ', '', $args['spatial_systems']));
    foreach($list as $system) {
    	$systems[$system] = lang::get("sref:$system");
    }
    $r .= data_entry_helper::sref_and_system(array(
    		'label' => lang::get('Grid Reference'),
    		'systems' => $systems
    	));

    $help = t('Use the search box to find a nearby town or village, then drag the map to pan and click on the map to set the centre grid reference of the transect. '.
    		'Alternatively if you know the grid reference you can enter it in the Grid Ref box above.');
    $r .= '<p class="ui-state-highlight page-notice ui-corner-all">'.$help.'</p>';
    $r .= data_entry_helper::georeference_lookup(array(
    		'label' => lang::get('Search for place'),
    		'driver'=>$args['georefDriver'],
    		'georefPreferredArea' => $args['georefPreferredArea'],
    		'georefCountry' => $args['georefCountry'],
    		'georefLang' => $args['language'],
    		'readAuth' => $auth['read']
    ));

    $options = iform_map_get_map_options($args, $auth['read']);
    $olOptions = iform_map_get_ol_options($args);
    if (!empty(data_entry_helper::$entity_to_load['sample:wkt'])) {
    	$options['initialFeatureWkt'] = data_entry_helper::$entity_to_load['sample:wkt'];
    }
    if (!isset($options['standardControls']))
    	$options['standardControls']=array('layerSwitcher','panZoomBar');
    $r .= map_helper::map_panel($options, $olOptions);

    $r .= '<input type="hidden" name="sample:sample_method_id" value="'.$args['transect_level_sample_method_id'].'" />';
    $r .= '<br/><input type="submit" value="'.lang::get('Save').'" />';
    $r .= '</form>';
    data_entry_helper::enable_validation('sample');
    return $r;
  }

  /**
   * Return the generated output for the species grid tab.
   * @param array $args List of parameter values passed through to the form depending on how the form has been configured.
   *                    This array always contains a value for language.
   * @param integer $nid The Drupal node object's ID.
   * @param object $auth The full read-write authorisation.
   * @return HTML.
   */
  public static function get_occurrences_tab($args, $nid, $auth) {
    global $user;
    global $indicia_templates;
    // initially not ajax due to uncertainty over mandatory status of sample attributes.
    
    // remove the ctrlWrap as it complicates the grid & JavaScript unnecessarily
    $oldCtrlWrapTemplate = $indicia_templates['controlWrap'];
    $indicia_templates['controlWrap'] = '{control}';
    data_entry_helper::add_resource('jquery_form');

    $defaults = helper_base::explode_lines_key_value_pairs($args['defaults']);
    $record_status = isset($defaults['occurrence:record_status']) ? $defaults['occurrence:record_status'] : 'C';
    
    // Can't display grid unless parent sample already exists.
    if (isset(data_entry_helper::$entity_to_load['sample:id']) && data_entry_helper::$entity_to_load['sample:id'] != "") {
      // have just posted an edit to the existing parent sample, so can use it to get the parent location id.
      $parentSampleId = data_entry_helper::$entity_to_load['sample:id'];
    } else return "";
    $sample = data_entry_helper::get_population_data(array(
      'table' => 'sample',
      'extraParams' => $auth['read'] + array('view'=>'detail','id'=>$parentSampleId,'deleted'=>'f'),
      'nocache' => true
    ));
    // TODO put in checks to ensure that sample survey matches $args, and sample exists, parent_id is NULL
    $sample=$sample[0];
    $date=$sample['date_start'];
    
    // find any attributes that apply to quadrat samples.
    // We need attribute list in both ordered and indexed formats
    $attributes = data_entry_helper::getAttributes(array(
      'valuetable'=>'sample_attribute_value',
      'attrtable'=>'sample_attribute',
      'key'=>'sample_id',
      'fieldprefix'=>'smpAttr',
      'extraParams'=>$auth['read'],
      'survey_id'=>$args['survey_id'],
      'sample_method_id'=>$args['quadrat_level_sample_method_id']
    ));
    $attributesIdx = data_entry_helper::getAttributes(array(
    		'valuetable'=>'sample_attribute_value',
    		'attrtable'=>'sample_attribute',
    		'key'=>'sample_id',
    		'fieldprefix'=>'smpAttr',
    		'extraParams'=>$auth['read'],
    		'survey_id'=>$args['survey_id'],
    		'sample_method_id'=>$args['quadrat_level_sample_method_id'],
    		'multiValue'=>false // ensures that array_keys are the list of attribute IDs.
    ));
    
    $subSamples = data_entry_helper::get_population_data(array(
      'report' => 'library/samples/samples_list_for_parent_sample',
      'extraParams' => $auth['read'] + array('sample_id'=>$parentSampleId,'date_from'=>'','date_to'=>'', 'sample_method_id'=>$args['quadrat_level_sample_method_id'], 'smpattrs'=>implode(',', array_keys($attributesIdx))),
      'nocache'=>true
    ));

    $occ_attributes = data_entry_helper::getAttributes(array(
    		'valuetable'=>'occurrence_attribute_value',
    		'attrtable'=>'occurrence_attribute',
    		'key'=>'occurrence_id',
    		'fieldprefix'=>'occAttr',
    		'extraParams'=>$auth['read'],
    		'survey_id'=>$args['survey_id'],
    		'multiValue'=>false // ensures that array_keys are the list of attribute IDs.
    ));
     
    $o = data_entry_helper::get_population_data(array(
        'report' => 'reports_for_prebuilt_forms/UKBMS/ukbms_occurrences_list_for_parent_sample',
        'extraParams' => $auth['read'] + array('sample_id'=>$parentSampleId,'survey_id'=>$args['survey_id'],'date_from'=>'','date_to'=>'','taxon_group_id'=>'',
            'smpattrs'=>'', 'occattrs'=>implode(',',array_keys($occ_attributes))),
        // don't cache as this is live data
        'nocache' => true
    ));
    // build an array of occurrence and attribute data keyed for easy lookup
    $occurrences = array();
    $taxalist = species_packages_get_species($user->uid, $args['survey_id']);
    foreach($o as $occurrence) {
        $occurrences[$occurrence['sample_id'].':'.$occurrence['taxa_taxon_list_id']] = array(
          'ttl_id'=>$occurrence['taxa_taxon_list_id'],
          'o_id'=>$occurrence['occurrence_id'],
          'processed'=>false
        );
        if(!in_array($occurrence['taxa_taxon_list_id'], $taxalist)) // ensure taxa list includes all old data. Required if going into view old data when list has changed.
        	$taxalist[] = $occurrence['taxa_taxon_list_id'];
        foreach($occ_attributes as $attr => $defn){
          $occurrences[$occurrence['sample_id'].':'.$occurrence['taxa_taxon_list_id']]['value_'.$attr] = $occurrence['attr_occurrence_'.$attr];
          $occurrences[$occurrence['sample_id'].':'.$occurrence['taxa_taxon_list_id']]['a_id_'.$attr] = $occurrence['attr_id_occurrence_'.$attr];
        }
    }
    
    $limit1 = $args['sample_attribute_id_1_limit'];
    if(!in_array($args['sample_attribute_id_1'], array_keys($attributesIdx)))
    	return "CONFIGURATION ERROR: Supplied primary sample loop attribute (ID ".$args['sample_attribute_id_1'].") is not in the list of sample attributes configured for this survey (Survey ID ".$args['survey_id'].", attribute IDs ".implode(',', array_keys($attributes))."). Ensure that the Indicia Cache is cleared.<br/>";
    $limit2 = $args['sample_attribute_id_2_limit'];
    if(isset($args['sample_attribute_id_2']) && $args['sample_attribute_id_2']!= "") {
    	if(!in_array($args['sample_attribute_id_2'], array_keys($attributesIdx)))
	    	return "CONFIGURATION ERROR: Supplied optional secondary sample loop attribute (ID ".$args['sample_attribute_id_2'].") is not in the list of sample attributes configured for this survey (Survey ID ".$args['survey_id'].", attribute IDs ".implode(',', array_keys($attributes))."). Ensure that the Indicia Cache is cleared.<br/>";
    } else $limit2 = 1;

	foreach($subSamples as $subSample){
		$attrVal = self::_get_sample_attr_value($subSample, $args['sample_attribute_id_1_limit']);
		if($attrVal > $limit1) $limit1 = $attrVal;
    	if(isset($args['sample_attribute_id_2']) && $args['sample_attribute_id_2']!= "") {
    		$attrVal = self::_get_sample_attr_value($subSample, $args['sample_attribute_id_2_limit']);
    		if($attrVal > $limit2) $limit2 = $attrVal;
    	}
	}
	
    $t = data_entry_helper::get_population_data(array(
    		'table' => 'taxa_taxon_list',
    		'extraParams' => $auth['read'] + array('view'=>'detail','id'=>$taxalist)));

    $r = '<form method="post" id="occurrences">' .
    		$auth['write'] .
    		'<input type="hidden" name="page" value="occurrences"/>' .
    		'<input type="hidden" name="website_id" value="'.$args['website_id'].'"/>' .
    		(isset(data_entry_helper::$entity_to_load['sample:id']) ?
      			'<input type="hidden" name="sample:id" value="'.data_entry_helper::$entity_to_load['sample:id'].'"/>' : '') .
			'<input type="hidden" name="sample:survey_id" value="'.$args['survey_id'].'"/>' .
			'<input type="hidden" id="occurrence:record_status" name="occurrence:record_status" value="'.$record_status.'" />' . "\n" .
			'<table id="transect-input1" class="ui-widget species-grid"><thead class="table-header">';
    
    // Build header
    $r .= '<tr>' .
    		'<th class="ui-widget-header">' . $attributesIdx[$args['sample_attribute_id_1']]['caption'] . '</th>'.
      		(!isset($args['sample_attribute_id_2']) || $args['sample_attribute_id_2'] == "" ? '' :
      			'<th class="ui-widget-header">' . $attributesIdx[$args['sample_attribute_id_2']]['caption'] . '</th>');
    foreach ($attributes as $attr) {
    	if($attr['attributeId'] != $args['sample_attribute_id_1'] &&
    			(!isset($args['sample_attribute_id_2']) || $args['sample_attribute_id_2'] == "" || $attr['attributeId'] != $args['sample_attribute_id_2']))
    		$r .= '<th class="ui-widget-header">'  . $attr['caption'] . '</th>';
    }
    // need to maintain order as defined in $taxalist
    $extras = explode(',',$args['taxon_headings_extras']);
    $extraDetails = array();
    foreach($extras as $extra) {
    	$parts = explode(':',$extra);
    	$extraDetails[$parts[0]] = $parts[1];
    }
    foreach ($taxalist as $ttlid) {
    	foreach($t as $taxon) {
    		if($ttlid == $taxon['id']) {
    			$attr = species_packages_get_species_attribute($user->uid, $args['survey_id'], $ttlid);
		    	$r .= '<th class="ui-widget-header"' .
    				($taxon['taxon'] != $taxon['common'] ? ' title="'.$taxon['taxon'].'"' : '') .
		    		'>' .
    				$taxon['common'] .
    				(isset($extraDetails[$attr]) ? ' ('.$extraDetails[$attr].')' : '').
    				(isset($extraDetails[$taxon['common']]) ? ' ('.$extraDetails[$taxon['common']].')' : '').
    				'</th>';
    		}
    	}
    }
    $r .= '</tr></thead><tbody class="ui-widget-content">';

    // fieldname naming conventions
    // Each sample attribute has a fieldname "Grid:Row<rowNumber>:[subsampleID]:smpAttr:<attributeID>:[<attributeValueID>]"
    // Each occurrence attribute has a fieldname "Grid:Row<rowNumber>:[subsampleID]:occ:<ttlid>:[occurrenceID]:occAttr:<attributeID>[:<attributeValueID>]"
	// If any smpAttr is filled in, or any occAttr is filled in, then the other occAttr on that line are set to zero
	// If any occAttr is set to zero, the zero abundance flag is set.
    $altRow = false;
    $defAttrOptions = array('extraParams'=>$auth['read']);
    $roAttrOptions = array('extraParams'=>$auth['read'], 'readonly'=>'readonly="readonly"', 'class' => 'ignore', 'ctrl' => 'hidden');
    $rowNumber = 1;
    
    unset($attributesIdx[$args['sample_attribute_id_1']]['caption']);
    if(isset($args['sample_attribute_id_2']) && $args['sample_attribute_id_2']!= "")
    	unset($attributesIdx[$args['sample_attribute_id_2']]['caption']);
	foreach ($attributes as &$attr)
		unset($attr['caption']);
	foreach ($occ_attributes as &$attr)
		unset($attr['caption']);
    
    for($loop1 = 1; $loop1 <= $limit1; $loop1++) {
    	for($loop2 = 1; $loop2 <= $limit2; $loop2++) {
    		
    		$existingSample = self::_get_row_sample($subSamples, $args['sample_attribute_id_1'], $loop1,
    				(isset($args['sample_attribute_id_2']) && $args['sample_attribute_id_2']!= "" ? $args['sample_attribute_id_2'] : false), $loop2);
  
    		$rowPrefix = "Grid:Row".$rowNumber.":".($existingSample ? $existingSample['sample_id'] : '').":";
    		$r .= '<tr class="datarow'./*($altRow?' odd':'').*/($loop2 == 1 ? ' level1' : ' level2').'">';

    	    $roAttrOptions['id'] = ($roAttrOptions['fieldname'] = self::_get_sample_attr_name($rowPrefix, $existingSample, $args['sample_attribute_id_1'])); 
    	    $roAttrOptions['default'] = $loop1;
    		$r .= '<td>' . data_entry_helper::outputAttribute($attributesIdx[$args['sample_attribute_id_1']], $roAttrOptions) . '</td>';
    		
    		if(isset($args['sample_attribute_id_2']) && $args['sample_attribute_id_2']!= "") {
    			$roAttrOptions['id'] = ($roAttrOptions['fieldname'] = self::_get_sample_attr_name($rowPrefix, $existingSample, $args['sample_attribute_id_2']));
    			$roAttrOptions['default'] = $loop2;
    			$r .= '<td>' . data_entry_helper::outputAttribute($attributesIdx[$args['sample_attribute_id_2']], $roAttrOptions) . '</td>';
    		}

    	    foreach ($attributes as $attr) {
    			if($attr['attributeId'] != $args['sample_attribute_id_1'] &&
    					(!isset($args['sample_attribute_id_2']) || $args['sample_attribute_id_2'] == "" || $attr['attributeId'] != $args['sample_attribute_id_2'])) {
    	    		unset($defAttrOptions['class']);
    	   			$defAttrOptions['id'] = ($defAttrOptions['fieldname'] = self::_get_sample_attr_name($rowPrefix, $existingSample, $attr['attributeId']));
    	    		$defAttrOptions['default']=self::_get_sample_attr_value($existingSample, $attr['attributeId']);
    	    		if($loop2 == 1 || !in_array($attr['attributeId'], explode(',', $args['level_1_attributes']))) {
    	    			if(in_array($attr['attributeId'], explode(',', $args['level_1_attributes'])))
    	    				$defAttrOptions['class']='lvl1Master lvl1-'.$loop1.'-'.$attr['attributeId'];
    	    			$r .= '<td>' . data_entry_helper::outputAttribute($attr, $defAttrOptions) . '</td>';
    				} else
     	   				$r .= '<td><input class="ignore copydown lvl1-'.$loop1.'-'.$attr['attributeId'].'" readonly="readonly" type="hidden" name="'.$defAttrOptions['fieldname'].'" value="'.$defAttrOptions['default'].'" /></td>';
    			}
    		}
    		// need to maintain order as defined in $taxalist
    		$first = true;
    		foreach ($taxalist as $ttlid) {
    			foreach($t as $taxon) {
    				if($ttlid == $taxon['id']) {
    					$defOccAttrOptions = array('extraParams'=>$auth['read']);
    					$attr = species_packages_get_species_attribute($user->uid, $args['survey_id'], $ttlid);
    					if($attr === false) // old data - ttl no longer in list, so no attr held : look up what is there 
    					{
    						foreach($occ_attributes as $attrID => $occ_attribute) {
    							if(isset($occurrences[$existingSample['sample_id'].':'.$ttlid]) &&
    									isset($occurrences[$existingSample['sample_id'].':'.$ttlid]['value_'.$attrID]) &&
    									$occurrences[$existingSample['sample_id'].':'.$ttlid]['value_'.$attrID] != '') {
    								$attr = $attrID;
    								break;
    							}
    						}
    					}
    					if($attr === false) { // old data - ttl no longer in list, no detectable attr held: enter dummy
							$r .= '<td title="Taxon no longer in Species Package. No old data found."></td>';
    					} else {
	    	   				$defOccAttrOptions['id'] = ($defOccAttrOptions['fieldname'] = self::_get_occurrence_attr_name($rowPrefix, $existingSample, $ttlid, $attr, $occurrences));
    		    			$defOccAttrOptions['default']=self::_get_occurrence_attr_value($existingSample, $ttlid, $attr, $occurrences);
							if($first) $defOccAttrOptions['class']='first';
							$first = false;
							$r .= '<td>' . data_entry_helper::outputAttribute($occ_attributes[$attr], $defOccAttrOptions) . '</td>';
    					}
    				}
    			}
    		}
    		$r .= '</tr>';
    		$altRow = !$altRow;
    		$rowNumber++;
    	}
    }
    $r .= '</table>';
    $r .= '<input type="submit" value="'.lang::get('Save').'" />';
    $r .= '</form>';
    
    data_entry_helper::enable_validation('occurrences');
    data_entry_helper::add_resource('jquery_ui');

    return $r;
  }

  /**
   * Return the generated output for the media grid tab.
   * @param array $args List of parameter values passed through to the form depending on how the form has been configured.
   *                    This array always contains a value for language.
   * @param integer $nid The Drupal node object's ID.
   * @param object $auth The full read-write authorisation.
   * @return HTML.
   */
  public static function get_media_tab($args, $nid, $auth) {
    global $user;
    
    data_entry_helper::add_resource('fancybox');
    data_entry_helper::add_resource('plupload');
    data_entry_helper::add_resource('jquery_ui');
    
    // Can't display media unless parent sample already exists.
    if (!self::$sampleId) return "";

    $r = '<form method="post" id="media">' .
    		$auth['write'] .
    		'<input type="hidden" name="page" value="media"/>' .
    		'<input type="hidden" name="website_id" value="'.$args['website_id'].'"/>' .
    		'<input type="hidden" name="sample:id" value="'.data_entry_helper::$entity_to_load['sample:id'].'"/>' .
    		'<input type="hidden" name="sample:survey_id" value="'.$args['survey_id'].'"/>' .
		    data_entry_helper::file_box(array(
    			'table' => 'sample_medium',
    			'caption' => lang::get('Photos'),
    			'codeGenerated' => 'all' // php?
    		)) .
		    '<input type="submit" value="'.lang::get('Save').'" />' .
    		'</form>';
    
    data_entry_helper::enable_validation('media');

    return $r;
  }

  /**
   * Function to return the specific subsample for the defined primary and optional secondary attribute values.
   */
  private static function _get_row_sample($subSamples, $attr_id_1, $attr_val_1, $attr_id_2, $attr_val_2)
  {
  	foreach($subSamples as $subSample){
  		if($subSample['attr_sample_'.$attr_id_1] == $attr_val_1 &&
  				($attr_id_2 === false || $subSample['attr_sample_'.$attr_id_2] == $attr_val_2))
  			return $subSample;
  	}
  	return false;
  }

  /**
   * Function to return the fieldname to be used by a sample attribute in the species grid.
   */
  private static function _get_sample_attr_name($prefix, $subSample, $attr_id)
  {
  	return $prefix.
    		"smpAttr:".
  			$attr_id.
  			($subSample && isset($subSample['attr_id_sample_'.$attr_id]) && $subSample['attr_id_sample_'.$attr_id] != '' ? ':'.$subSample['attr_id_sample_'.$attr_id] : '');
  }

  /**
   * Function to return the value to be used by a sample attribute in the species grid.
   */
  private static function _get_sample_attr_value($subSample, $attr_id)
  {
  	if($subSample &&
  			isset($subSample['attr_sample_'.$attr_id]) &&
  			$subSample['attr_sample_'.$attr_id] != '')
  		return $subSample['attr_sample_'.$attr_id];
  	else return '';
  }

  /**
   * Function to return the fieldname to be used by a occurrence attribute in the species grid.
   */
  private static function _get_occurrence_attr_name($prefix, $subSample, $ttlid, $attr_id, $occurrences)
  {
  	return $prefix.
  			'occ:'.
  			$ttlid.':'.
  			($subSample && isset($occurrences[$subSample['sample_id'].':'.$ttlid]) ? $occurrences[$subSample['sample_id'].':'.$ttlid]['o_id'] : '').':'.
  			'occAttr:'.
  			$attr_id.
  			($subSample && isset($occurrences[$subSample['sample_id'].':'.$ttlid]) && isset($occurrences[$subSample['sample_id'].':'.$ttlid]['a_id_'.$attr_id])
  					? ':'.$occurrences[$subSample['sample_id'].':'.$ttlid]['a_id_'.$attr_id] : '');
  }

  /**
   * Function to return the value to be used by a sample attribute in the species grid.
   */
  private static function _get_occurrence_attr_value($subSample, $ttlid, $attr_id, $occurrences)
  {
  	if($subSample &&
  			isset($occurrences[$subSample['sample_id'].':'.$ttlid]) &&
  			isset($occurrences[$subSample['sample_id'].':'.$ttlid]['value_'.$attr_id]) &&
  			$occurrences[$subSample['sample_id'].':'.$ttlid]['value_'.$attr_id] != '')
  		return $occurrences[$subSample['sample_id'].':'.$ttlid]['value_'.$attr_id];
  	else return '';
  }
  
  
  /**
   * Handles the construction of a submission array from a set of form values.
   * @param array $values Associative array of form data values.
   * @param array $args iform parameters.
   * @return array Submission structure.
   */
  public static function get_submission($values, $args) {
    $subsampleModels = array();
    $submission = submission_builder::build_submission($values, array('model' => 'sample'));
    if (isset($values['page']) && $values['page']=='occurrences') {
    	foreach($values as $key => $value){
    		$parts = explode(':', $key, 5);
    		if ($parts[0] == 'Grid') {
    			$details = explode(':', $parts[4], 3);
    			if(!isset($subsampleModels[$parts[1]])) {
	    			$smp = array('fkId' => 'parent_id',
	    					'model' => array('id' => 'sample',
	    							'fields' => array('sample_method_id'=> array('value' => $args['quadrat_level_sample_method_id']))),
	    					'data' => ($parts[2] != ''),
							'copyFields' => array('survey_id'=>'survey_id',
									'date_start'=>'date_start','date_end'=>'date_end','date_type'=>'date_type',
									'entered_sref_system' => 'entered_sref_system',
									'entered_sref' => 'entered_sref')); // from parent->to child
    				// TODO location_name
    				if($parts[2] != '')
    					$smp['model']['fields']['id'] = array('value' => $parts[2]);
    				$subsampleModels[$parts[1]]=$smp;
    			}
    			if($parts[3] == 'smpAttr') {
    				$subsampleModels[$parts[1]]['model']['fields']['smpAttr:'.$parts[4]] = array('value' => $value);
    				if($details[0] != $args['sample_attribute_id_1'] &&
    						(!isset($args['sample_attribute_id_2']) || $args['sample_attribute_id_2'] == '' || $details[0] != $args['sample_attribute_id_2']))
    					$subsampleModels[$parts[1]]['data'] = $subsampleModels[$parts[1]]['data'] || ($value != '');
	    		}
    		    if($parts[3] == 'occ') {
	    			$occ = array('fkId' => 'sample_id',
	    					'model' => array('id' => 'occurrence',
	    									'fields' => array('taxa_taxon_list_id' => array('value' => $details[0]),
	    											'website_id' => array('value' => $values['website_id']),
	    											'record_status' => array('value' => $values['occurrence:record_status']),
	    											'zero_abundance' => array('value' => ($value != '0' ? 'f' : 't')),
	    											$details[2] => array('value' => $value))));
	    			if($details[1] != '') $occ['model']['fields']['id'] = array('value' => $details[1]);
    		    	if($value != '' || $details[1] != '') {
    		    		if(!isset($subsampleModels[$parts[1]]['model']['subModels']))
    		    			$subsampleModels[$parts[1]]['model']['subModels'] = array($occ);
    		    		else
    		    			$subsampleModels[$parts[1]]['model']['subModels'][] = $occ;
    		    		$subsampleModels[$parts[1]]['data'] = true;
    		    	}
	    		}
    		}
    	}
    	foreach($subsampleModels as $row => $data)
    		if(!$subsampleModels[$row]['data'])
    			unset($subsampleModels[$row]);
    } else if (isset($values['page']) && $values['page']=='mainSample' && isset($values['sample:id']) && $values['sample:id'] != '') {
		// submitting the first page, with top level sample details: Filter down any change in Sref and date.
		$read = array(
			'nonce' => $values['read_nonce'],
			'auth_token' => $values['read_auth_token']
		);
		$existingSubSamples = data_entry_helper::get_population_data(array(
			'table' => 'sample',
			'extraParams' => $read + array('view'=>'detail','parent_id'=>$values['sample:id'],'deleted'=>'f'),
			'nocache' => true // may have recently added or removed a section
		));
		foreach($existingSubSamples as $existingSubSample){
			// TODO only do if fields actually changed.
       		$subsampleModels[] = array('fkId' => 'parent_id',
      				'model' => array('id' => 'sample',
      						'fields' => array('sample_method_id'=> array('value' => $args['quadrat_level_sample_method_id']),
      											'id' => array('value' => $existingSubSample['id']))),
      				'copyFields' => array('survey_id'=>'survey_id',
      						'date_start'=>'date_start','date_end'=>'date_end','date_type'=>'date_type',
      						'entered_sref_system' => 'entered_sref_system',
      						'entered_sref' => 'entered_sref')); // from parent->to child
      				// TODO location_name
      	}
    }
    	
    if(count($subsampleModels)>0)
      $submission['subModels'] = array_values($subsampleModels);
    return($submission);
  }

}
