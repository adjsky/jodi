---
paths:
  - 'app/Domain/*/Actions/**'
---

# Actions

## Domain actions use JodiAction
Implement domain operations as JodiAction subclasses. Put reusable logic in handle(), and add asController() when the action is a route handler.

## Use implicit route model binding
Type-hint domain models on Action controller entrypoints and rely on implicit route model binding.

## Authorize controller Actions through policies
Implement controller Action authorization in authorize() and call can() on the authenticated user against the relevant policy ability.

## JSON responses from output Data
For JSON read endpoints, transform models into the domain's output Data objects and return them with response()->json().

## Named-route redirects
Build web redirects from named routes with to_route() or redirect()->route(), not literal URLs or controller actions.
