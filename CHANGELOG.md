# Changelog

All notable changes to Nayori Workspaces ExamplePlugin are documented here.

## [0.1.1] - 2026-07-22

### Changed

- Create and edit forms explicitly compose the shared `@ui/FormActions.vue`
  primitive through the Core `FormPage` action slot
- The reference plugin continues to inherit field rendering, validation,
  responsive layout, and workspace behavior from shared Core components

### Compatibility

- Nayori Workspaces Core `^0.1.40`
- PHP `^8.2`
- Laravel `^12.0`

## [0.1.0] - 2026-07-16

Initial Plugin SDK v1 reference release.

### Added

- Business-scoped example records with explicit view/manage authorization
- Shared metadata-driven list, create, show, and edit pages
- Status-based archive and restore lifecycle
- Shared workspace mutation delivery and optimistic locking
- Cross-business isolation, authorization, mutation, stale-write, and assembled
  login/business-selection CRUD smoke coverage

### Compatibility

- Nayori Workspaces Core `^0.1.0`
- PHP `^8.2`
- Laravel `^12.0`

[0.1.0]: https://github.com/Nayori-jp/Nayori.Workspaces.ExamplePlugin/releases/tag/v0.1.0
