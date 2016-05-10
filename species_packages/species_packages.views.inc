<?php

function species_packages_views_data_alter(&$data) {
  $data['users']['species_package'] = array(
    'title' => t('Species Packages'),
    'help' => t('Displays the Species Packages for the user.'),
    'field' => array(
      'handler' => 'views_handler_field_species_package',
    )
  );
}
