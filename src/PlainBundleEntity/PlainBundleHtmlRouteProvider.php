<?php

namespace Drupal\entity_admin_handlers\PlainBundleEntity;

use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\entity_admin_handlers\PlainBundleEntity\PlainBundleAdminController;
use Symfony\Component\Routing\Route;

/**
 * Route provider for entity types using non-entity bundles.
 *
 * The use cases for this include entity types whose bundles are derived from
 * plugins using the Entity API contrib module's functionality, or simnply
 * hardcoded in hook_entity_bundle_info().
 *
 * This handler requires the entity type to have a 'field-ui-base' link
 * template. This should be the path at which to show a list of bundles, for
 * example, '/admin/structure/my-entity-type'. The controller for this route
 * will show the list of bundles, with operation links to the Field UI admin
 * pages for managine fields and displays.
 *
 * The handler will define a route 'entity.{$entity_type_id}.field_ui_base'
 * which the entity type should declare in its annotation. The path for this
 * will be the 'field-ui-base' link template path with a '{bundle}' path
 * component appended to it.
 *
 * @see \Drupal\entity_admin_handlers\PlainBundleEntity\PlainBundleAdminController
 */
class PlainBundleHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    $entity_type_id = $entity_type->id();

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

    if ($admin_base_route = $this->getAdminBaseRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.admin", $admin_base_route);
    }

    if ($field_ui_base_route = $this->getFieldUIBaseRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.field_ui_base", $field_ui_base_route);
    }

    return $collection;
  }

  /**
   * Gets the admin base route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getAdminBaseRoute(EntityTypeInterface $entity_type) {
    $admin_permission = $entity_type->getAdminPermission();

    $route = new Route($entity_type->getLinkTemplate('field-ui-base'));
    $route->setDefault('_controller', PlainBundleAdminController::class . '::adminPage');
    $route->setDefault('_title', '@entity-label field settings');
    $route->setDefault('_title_arguments', [
      '@entity-label' => $entity_type->getLabel(),
    ]);
    $route->setDefault('entity_type_id', $entity_type->id());
    $route->setRequirement('_permission', $admin_permission);

    return $route;
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
    $admin_permission = $entity_type->getAdminPermission();

    // Append a path component for the bundle parameter. The parameter must be
    // '{bundle}', as this is what
    // Drupal\field_ui\Routing\RouteSubscriber::alterRoutes() expects.
    $route = new Route($entity_type->getLinkTemplate('field-ui-base') . '/{bundle}');

    $route->setDefault('_controller', PlainBundleAdminController::class . '::bundlePage');
    $route->setDefault('_title', '@entity-label field settings');
    $route->setDefault('_title_arguments', [
      '@entity-label' => $entity_type->getLabel(),
    ]);
    $route->setDefault('entity_type_id', $entity_type->id());
    $route->setRequirement('_permission', $admin_permission);

    return $route;
  }

}
