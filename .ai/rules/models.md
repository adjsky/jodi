---
paths:
  - 'app/Domain/*/Models/**'
---

# Models

## Allow-list mass assignment
Declare mass-assignable model attributes with $fillable rather than using a $guarded block-list.

## Use custom Eloquent builders
Put reusable query behavior in the shared custom Builder hierarchy and attach concrete builders to models with UseEloquentBuilder.

## Use Laravel model attributes
Declare mass-assignable fields with Laravel 13's #[Fillable] attribute and serialized exclusions with #[Hidden]. Omit empty declarations when Eloquent's guarded and visibility defaults already provide the intended behavior.
