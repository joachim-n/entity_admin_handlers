<?php

namespace Drupal\entity_admin_handlers\SingleBundleEntity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Route provider for entities with a single bundle.
 *
 * This handler requires the entity type to have a 'field-ui-base' link
 * template. This should be a path such as, '/admin/structure/my-entity-type'.
 * The controller for this route shows a page with a brief message, and Field UI
 * will add tabs alongside this page.
 */
class SingleBundleEntityHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    // Sanity checks.
    if (!$entity_type->hasLinkTemplate('field-ui-base')) {
      throw new UnsupportedEntityTypeDefinitionException(sprintf(
        "The %s entity type uses PlainBundleHtmlRouteProvider but does not define a 'field-ui-base' link template.",
        $entity_type_id
      ));
    }
    if (!$entity_type->getAdminPermission()) {
      throw new UnsupportedEntityTypeDefinitionException(sprintf(
        "The %s entity type uses PlainBundleHtmlRouteProvider but does not define an admin permission.",
        $entity_type_id
      ));
    }
    // TODO: consider adding 'field_ui_base_route' dynamically in
    // hook_entity_type_alter(), since the route name is derived.
    if (!$entity_type->get('field_ui_base_route')) {
      throw new UnsupportedEntityTypeDefinitionException(sprintf(
        "The %s entity type uses PlainBundleHtmlRouteProvider but does not define a field_ui_base_route entity type property.",
        $entity_type_id
      ));
    }

    $entity_type_id = $entity_type->id();

    if ($field_ui_base_route = $this->getFieldUIBaseRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.field_ui_base", $field_ui_base_route);
    }

    return $collection;
  }

  /**
   * Gets the field UI base route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getFieldUIBaseRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('field-ui-base'));
    $route->setDefault('_controller', SingleBundleAdminController::class . '::content');
    $route->setDefault('_title', '@entity-label field settings');
    $route->setDefault('_title_arguments', [
      '@entity-label' => $entity_type->getLabel(),
    ]);
    $route->setDefault('entity_type_id', $entity_type->id());
    $route->setRequirement('_permission', $admin_permission);

    return $route;
  }

}
