<?php

namespace Drupal\entity_admin_handlers\PlainBundleEntity;

use Drupal\Core\Entity\Menu\DefaultContentEntityLinksProvider;

/**
 * Link provider for entity types using non-entity bundles.
 *
 * @see \Drupal\entity_admin_handlers\PlainBundleEntity\PlainBundleHtmlRouteProvider
 * @see \Drupal\entity_admin_handlers\PlainBundleEntity\PlainBundleAdminController
 */
class PlainBundleLinksProvider extends DefaultContentEntityLinksProvider {

  /**
   * {@inheritdoc}
   */
  public function getMenuLinks($base_plugin_definition) {
    $link_derivative_plugins = parent::getMenuLinks($base_plugin_definition);

    if ($field_ui_link = $this->getAdminMenuLink($base_plugin_definition)) {
      $link_derivative_plugins[$this->getRouteName('admin')] = $field_ui_link;
    }

    return $link_derivative_plugins;
  }

  /**
   * Provides the admin menu link.
   *
   * @param array $base_plugin_definition
   *   The base link plugin definition.
   *
   * @return array|null
   *   The plugin definition, or NULL if no link should be provided.
   */
  protected function getAdminMenuLink($base_plugin_definition) {
    if ($this->routeExists($this->getRouteName('admin'))) {
      $link = $base_plugin_definition;

      $link['title'] = t('@entity-label field settings', [
        '@entity-label' => $this->entityType->getLabel(),
      ]);
      $link['description'] = t('Create and manage fields, forms, and display settings for @plural-label.', [
        '@plural-label' => $this->entityType->getPluralLabel(),
      ]);
      $link['route_name'] = $this->getRouteName('admin');
      $link['parent'] = 'system.admin_structure';

      return $link;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTaskLinks($base_plugin_definition) {
    $task_derivative_plugins = parent::getTaskLinks($base_plugin_definition);

    // We don't provide a task link for the field_ui_base route (e.g.
    // 'admin/structure/ENTITY_TYPE/BUNDLE/fields'). The route itself is
    // necessary for Field UI's assumptions about how it will be used, but the
    // task links that Field UI provides are OK without the task for the base
    // route being there. This means that there is no link anywhere in the UI to
    // the field UI base route, which is intended, as it's a page with only
    // dummy content.

    return $task_derivative_plugins;
  }

}
