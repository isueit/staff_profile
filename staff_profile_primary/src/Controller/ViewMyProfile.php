<?php

namespace Drupal\staff_profile_primary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides route responses for staff_profile_primary module
 */
class ViewMyProfile extends ControllerBase
{

  /**
   * Redirect to User's profile page, if found.
   *
   * @return array
   *   A simple render array.
   */
  public function view_my_profile()
  {
    // Deny any page caching on the current request.
    \Drupal::service('page_cache_kill_switch')->trigger();

    $results = 'Profile not found!';

    // Skip it if logged in as admin
    $currentUser = \Drupal::currentUser();
    if ($currentUser->id() == 1) {
      $results = 'No staff profile for Admin user';
    } else {
      // Get all the staff_profile nodes
      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'staff_profile')
        ->execute();
      $nodes = Node::loadMultiple($nids);

      // Step through the nodes, redirect when the current user is the owner of the node
      foreach ($nodes as $node) {
        if ($node->getOwner()->getUsername() == $currentUser->getUsername()) {
          $response = new RedirectResponse('node/' . $node->id(), '302');
          $response->send();
        }
      }
      \Drupal::logger('staff_profile_primary')->info('Profile not found: ' . $currentUser->getUsername());
    }

    // Return the results, when no redirect found
    $element = array(
      '#title' => 'View My Profile',
      '#markup' => $results,
    );

    return $element;
  }
}
