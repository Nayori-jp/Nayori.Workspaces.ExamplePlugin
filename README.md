# Nayori.Workspaces.ExamplePlugin

Example add-on for Nayori Workspaces demonstrating how a plugin can connect to
the shared CRUD system with minimal code.

It provides:

- one example table
- one metadata-driven list page
- one shared-style detail page

## Workspace contract

Example records belong to a business. All plugin routes resolve the current
business, list and record queries are business-scoped, and records from another
business return `404`.

Business members may view the plugin. Only owners and administrators may
create or edit records. Future plugin resources should follow the same explicit
view/manage authorization pattern unless they define a more granular policy.

The example uses status-based lifecycle management: deleting from the CRUD UI
sets the record to `archived`, and restore returns it to `active`. Create,
update, archive, and restore operations emit the shared workspace mutation
payload. Edit updates also require the shared `workspace_version` value.
