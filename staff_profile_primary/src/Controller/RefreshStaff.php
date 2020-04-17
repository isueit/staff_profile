<?php

namespace Drupal\staff_profile_primary\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route response to refresh the staff profiles from the database
 */
class RefreshStaff extends ControllerBase {
  /**
   * Refresh the Staff Profiles from database
   *
   * @return array
   *   A simple render array
   */

  public function refresh_staff() {

    \Drupal::logger('staff_profile_primary')->info('Starting staff profile refresh from web page');
    staff_profile_primary_profile_from_database();
    $results = '<p>Complete!</p>';

    $element = array(
      '#title' => 'Refresh Staff Profiles',
      '#markup' => $results,
    );
    return $element;
  }
}
