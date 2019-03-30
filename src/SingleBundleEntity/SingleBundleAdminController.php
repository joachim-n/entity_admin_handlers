<?php

namespace Drupal\entity_admin_handlers\SingleBundleEntity;

use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Controller for the field UI base route.
 */
class SingleBundleAdminController {

  /**
   * Callback for the field UI base route.
   */
  public function content(RouteMatchInterface $route_match) {
    $entity_type_id = $route_match->getRouteObject()->getDefault('entity_type_id');
    $entity_type = \Drupal::service('entity_type.manager')->getDefinition($entity_type_id);

    return [
      '#markup' => t('@label entity settings.', [
        '@label' => $entity_type->getLabel(),
      ]),
    ];
  }

}
