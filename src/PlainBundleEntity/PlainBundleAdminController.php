<?php

namespace Drupal\entity_admin_handlers\PlainBundleEntity;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Controller for admin routes for entity types using non-entity bundles.
 */
class PlainBundleAdminController {

  use StringTranslationTrait;

  /**
   * Callback for the admin overview route.
   */
  public function adminPage(RouteMatchInterface $route_match) {
    $entity_type_id = $route_match->getRouteObject()->getDefault('entity_type_id');
    $entity_type = \Drupal::service('entity_type.manager')->getDefinition($entity_type_id);
    $entity_bundle_info = \Drupal::service('entity_type.bundle.info')->getBundleInfo($entity_type_id);

    $build = [];

    $build['table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Name'),
        $this->t('Description'),
        $this->t('Operations'),
      ],
      '#rows' => [],
      '#empty' => $this->t('There are no @label yet.', [
        '@label' => $entity_type->getPluralLabel(),
      ]),
    ];

    foreach ($entity_bundle_info as $bundle_name => $bundle_info) {
      $build['table']['#rows'][$bundle_name] = [
        'name' => ['data' => $bundle_info['label']],
        'description' => ['data' => $bundle_info['description']],
        'operations' => ['data' => $this->buildOperations($entity_type_id, $bundle_name)],
      ];
    }

    return $build;
  }

  /**
   * Callback for the field UI base route.
   */
  public function bundlePage(RouteMatchInterface $route_match, $bundle = NULL) {
    $entity_type_id = $route_match->getRouteObject()->getDefault('entity_type_id');
    $entity_type = \Drupal::service('entity_type.manager')->getDefinition($entity_type_id);
    $entity_bundle_info = \Drupal::service('entity_type.bundle.info')->getBundleInfo($entity_type_id);

    return [
      '#markup' => $this->t('The @bundle-label bundle has no settings.', [
        '@bundle-label' => $entity_bundle_info[$bundle]['label'],
      ]),
    ];
  }

  /**
   * Builds a renderable list of operation links for the bundle.
   *
   * @param array $bundle_info
   *   The bundle info for the bundle on which the linked operations will be
   *   performed.
   *
   * @return array
   *   A renderable array of operation links.
   *
   * @see \Drupal\Core\Entity\EntityListBuilder::buildRow()
   */
  public function buildOperations($entity_type_id, $bundle_name) {
    $operations = [];

    // Copy the work of field_ui_entity_operation(), which we can't use because
    // it expects an entity.
    // TODO: permissions
    $operations['manage-fields'] = [
      'title' => t('Manage fields'),
      'weight' => 15,
      'url' => Url::fromRoute("entity.{$entity_type_id}.field_ui_fields", [
        'bundle' => $bundle_name,
      ]),
    ];
    $operations['manage-form-display'] = [
      'title' => t('Manage form display'),
      'weight' => 20,
      'url' => Url::fromRoute("entity.entity_form_display.{$entity_type_id}.default", [
        'bundle' => $bundle_name,
      ]),
    ];
    $operations['manage-display'] = [
      'title' => t('Manage display'),
      'weight' => 25,
      'url' => Url::fromRoute("entity.entity_form_display.{$entity_type_id}.default", [
        'bundle' => $bundle_name,
      ]),
    ];

    $build = [
      '#type' => 'operations',
      '#links' => $operations
    ];

    return $build;
  }

}
