<?php

namespace Drupal\entity_admin_handlers\SingleBundleEntity;

use Drupal\Core\Entity\Menu\DefaultContentEntityLinksProvider;

/**
 * Links provider for entities with a single bundle.
 */
class SingleBundleEntityLinksProvider extends DefaultContentEntityLinksProvider {

  /**
   * {@inheritdoc}
   */
  public function getMenuLinks($base_plugin_definition) {
    $link_derivative_plugins = parent::getMenuLinks($base_plugin_definition);

    if ($field_ui_link = $this->getFieldUIBaseMenuLink($base_plugin_definition)) {
      $link_derivative_plugins[$this->getRouteName('field_ui_base')] = $field_ui_link;
    }

    return $link_derivative_plugins;
  }

  /**
   * Provides the field UI base menu link.
   *
   * @param array $base_plugin_definition
   *   The base link plugin definition.
   *
   * @return array|null
   *   The plugin definition, or NULL if no link should be provided.
   */
  protected function getFieldUIBaseMenuLink($base_plugin_definition) {
    if ($this->routeExists($this->getRouteName('field_ui_base'))) {
      $link = $base_plugin_definition;

      $link['title'] = t('@entity-label field settings', [
        '@entity-label' => $this->entityType->getLabel(),
      ]);
      $link['description'] = t('Create and manage fields, forms, and display settings for @plural-label.', [
        '@plural-label' => $this->entityType->getPluralLabel(),
      ]);
      $link['route_name'] = $this->getRouteName('field_ui_base');
      $link['parent'] = 'system.admin_structure';

      return $link;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTaskLinks($base_plugin_definition) {
    $task_derivative_plugins = parent::getTaskLinks($base_plugin_definition);

    if ($field_ui_link = $this->getFieldUIBaseTaskLink($base_plugin_definition)) {
      $task_derivative_plugins[$this->getRouteName('field_ui_base')] = $field_ui_link;
    }


    return $task_derivative_plugins;
  }

  /**
   * Provides the field UI base task link.
   *
   * This is needed for Field UI's links to hang off of.
   *
   * @param array $base_plugin_definition
   *   The base link plugin definition.
   *
   * @return array|null
   *   The plugin definition, or NULL if no link should be provided.
   */
  protected function getFieldUIBaseTaskLink($base_plugin_definition) {
    $field_ui_base_route_name = $this->getRouteName('field_ui_base');
    if ($this->routeExists($field_ui_base_route_name)) {
      $link = $base_plugin_definition;

      $link['title'] = $this->t('Settings');
      $link['route_name'] = $field_ui_base_route_name;
      $link['base_route'] = $field_ui_base_route_name;

      return $link;
    }
  }

}
