<?php

namespace Drupal\entity_admin_handlers\SingleBundleEntity;

/**
 * Controller for the field UI base route.
 */
class SingleBundleAdminController {

  /**
   * Callback for the field UI base route.
   */
  public function content() {
    return [
      '#markup' => t('Accounting client entity settings.'),
    ];
  }

}
