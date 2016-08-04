<?php
/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
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
 * @package Client
 * @subpackage PrebuiltForms
 * @author  Indicia Team
 * @license http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link  http://code.google.com/p/indicia/
 */

require_once('dynamic_report_explorer.php');

/**
 * Class defining a Cocoast specific "who" filter - recorder and hub selection.
 */
class cocoast_filter_who extends filter_base {

	public function get_title() {
		return lang::get('Who');
	}

	/**
	 * Define the HTML required for this filter's UI panel.
	 */
	public function get_controls() {
		$r = '<div class="context-instruct messages warning">' . lang::get('Please note, you cannnot change this setting because of your access permissions in this context.') . '</div>';
		$r .= data_entry_helper::checkbox(array(
				'label' => lang::get('Only include my records'),
				'fieldname' => 'my_records'
		));
		$vocabulary = taxonomy_vocabulary_machine_name_load('hubs');
		$terms = entity_load('taxonomy_term', FALSE, array('vid' => $vocabulary->vid));
		// the hub is driven by a user field, stored as tid.
		$r .= '<fieldset><legend>'.lang::get('Members of Hub:').'</legend>';
		$r .= "<p id=\"who-hub-instruct\">".
				lang::get('Please note that this converts each Hub into a list of users associated with the Hub, and fetches the data created by those users.').
				"</p>\n";
		$hubList = array();
		foreach($terms as $term) {
			$hubList[] = array($term->tid, $term->name);
			// TODO Cache
			$query = new EntityFieldQuery();
			$query->entityCondition('entity_type', 'user')
				->fieldCondition('field_preferred_training_hub', 'tid', $term->tid);
			$result = $query->execute();
			// This gives us the CMS user ID: now convert to 
			$userIDList = array();
			if(count($result)==0) $userIDList = array(-1);
			else {
				$cmsUserIDs = array_keys($result['user']);
				foreach($cmsUserIDs as $cmsUserID) {
					$user_data = user_load($cmsUserID);
					// TODO Making assumption about language
					if (!empty($user_data->field_indicia_user_id['und'][0]['value'])) {
						$userIDList[] = $user_data->field_indicia_user_id['und'][0]['value'];
					}
				}
				if(count($userIDList) == 0) $userIDList = array(-1);
			}
			$userIDList = array_unique($userIDList);
			data_entry_helper::$javascript .= "indiciaData.hub".$term->tid." = '" .implode(',',$userIDList)."';\n";
			$r .= data_entry_helper::checkbox(array(
					'label' => $term->name,
					'fieldname' => 'hub'.$term->tid,
					'helpText' => ($userIDList[0] == -1 ? 'No' : count($userIDList)).lang::get(' users.')
			));
		}
		data_entry_helper::$javascript .= "indiciaData.hubList = ".json_encode($hubList).";\n";
		$r .= '</fieldset>';
		return $r;
	}
}

/**
 * Provides a dynamically output page which can contain a map and several reports, potentially
 * organised onto several tabs.
 * @package Client
 * @subpackage PrebuiltForms
 */
class iform_cocoast_dynamic_report_explorer extends iform_dynamic_report_explorer {
  
  /** 
   * Return the form metadata.
   */
  public static function get_cocoast_dynamic_report_explorer_definition() {
    return array(
      'title' => 'Cocoast Reporting page (customisable)',
      'category' => 'Cocoast Specific',
      'description' => 'Provides a dynamically output page which can contain a map and several reports, potentially '.
          'organised onto several tabs.',
      'recommended' => true
    );
  }

  /**
   * Get the list of parameters for this form.
   * @return array List of parameters that this form requires.
   */
  // get_parameters is same as parent
  // getHeader, getFooter, getFirstTabAdditionalContent same as parent
  // get_form same as parent  
  // all controls are the same
   
  protected static function get_control_standardparams($auth, $args, $tabalias, $options) {
  	$options['generateFilterListCallback'] = array('iform_cocoast_dynamic_report_explorer', 'generateFilterListCallback');
  	return parent::get_control_standardparams($auth, $args, $tabalias, $options);
  }
  
  public static function generateFilterListCallback($entity) {
  	if ($entity==='occurrence') {
  	  $filters = array(
  			'filter_what'=>new filter_what(),
  			'filter_where'=>new filter_where(),
  			'filter_when'=>new filter_when(),
  			'filter_who'=>new cocoast_filter_who(),
  			'filter_occurrence_id'=>new filter_occurrence_id(),
  			'filter_quality'=>new filter_quality(),
  			'filter_source'=>new filter_source()
      );
    } elseif ($entity==='sample') {
  	  $filters = array(
  			'filter_where'=>new filter_where(),
  			'filter_when'=>new filter_when(),
  			'filter_who'=>new filter_who(),
  			'filter_sample_id'=>new filter_sample_id(),
  			'filter_quality'=>new filter_quality_sample(),
  			'filter_source'=>new filter_source()
  	  );
    }
    return $filters;
  }
  
  
}