# Introduction

The Entity Admin Handlers module provides an out-of-the box admin UI for custom
entity types whose configuration means that the admin UI code in Drupal core
doesn't fit.

This reduces the need for boilerplate code to provide a UI to manage fields on
the entity type and its bundles.

The module provides:

- a route provider handler
- an entity links handler
- where required, a controller for any routes that are defined.

## Requirements

This module expects the patch at
https://www.drupal.org/project/drupal/issues/2976861 to be applied in order to
provide menu, task, and action links. The routes will work without it, however.

## Usage

To use this module with an entity type, set the 'route_provider' and
'link_provider' handlers to those from a particular set.

For example:

```
 *     "route_provider" = {
 *       "html" = "Drupal\entity_admin_handlers\SingleBundleEntity\SingleBundleEntityHtmlRouteProvider",
 *     },
 *     "link_provider" = "Drupal\entity_admin_handlers\SingleBundleEntity\SingleBundleEntityLinksProvider",
```

The entity type must also define:

- a 'field-ui-base' link template
- an admin permission
- the field_ui_base_route property, which must be set to
  'entity.ENTITY_TYPE.field_ui_base'.

## Entity type configurations

The following cases are provided for.

## Single bundle entity type

This is for entity types that do not use bundles and in effect, have a single
bundle which typically has the same name as the entity type.

This is identical to the way the core user entity type works.

The handlers define a dummy route for Field UI module to hang its routes off.

## Plain bundle entity type

This is for entity types that use multiple bundles, but do not have a config
entity type that defines the bundles (so for example, if the content entity type
is 'foo', there is no 'foo_type' entity type).

Such an entity type may have its bundles defined in hook_entity_bundle_info()
implementations, or have then derived from plugins using the Entity API contrib
module's bundle plugins functionality.

The handlers define a route that lists the bundles, and a dummy route for each
bundle for Field UI module to hang its routes off.
