<?php

namespace Drupal\h5p_xapi\Plugin\views\access;

use Drupal\views\Plugin\views\access\AccessPluginBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;

use Symfony\Component\Routing\Route;

/**
 * Access plugin that provides access control for viewing the H5P XAPI Event Result Data page
 *
 * @ingroup views_access_plugins
 *
 * @ViewsAccess(
 *   id = "h5pxapiviewresultspage",
 *   title = @Translation("H5P XAPI Views Results Page"),
 *   help = @Translation("H5P XAPI Views Results Page")
 * )
 */

class H5PXAPIResultsPage extends AccessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account) {
    if (!empty(array_intersect(['administrator', 'content_moderator', 'community_manager'], $account->getRoles()))) {
      return TRUE;
    }

    $current_path = explode('/', \Drupal::service('path.current')->getPath());
    
    if (is_numeric($current_path[3]) && is_numeric($current_path[4])) {
      $uid = $current_path[3];
      $nid = $current_path[4];

      $owner_node = User::load($uid);

      if ($owner_node) {
        $h5p_node = Node::load($nid);

        if ($h5p_node) {
          $author = $h5p_node->getOwner();
          $author_id = $author->id();

          /* first, let's check if the current user is the owner of the node identified by $nid */
          if ($author_id == $uid) {
            return TRUE;
          } else {
            /* TODO: Check if the current user has interacted with the node identified by $nid */
            return TRUE;
          }
        }
      }
    } else {
      return FALSE;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function alterRouteDefinition(Route $route) {
    $route->setRequirement('_access', 'TRUE');
  }
}
