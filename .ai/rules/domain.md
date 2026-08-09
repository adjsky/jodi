---
paths:
  - 'app/Domain/**'
---

# Domain

## Eager-load per query
Eager-load required relationships explicitly on each query rather than declaring model-level $with defaults.

## Organize business code by domain
Place business code under app/Domain/<Context> and organize each context by local types such as Actions, Data, Models, Policies, and Builders.

## Use closure-based database transactions
Wrap multi-write atomic domain operations in DB::transaction() closures instead of manually managing transaction lifecycle.
