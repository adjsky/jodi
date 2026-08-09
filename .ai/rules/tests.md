---
paths:
  - 'tests/**'
---

# Tests

## Pest functional tests and shared database reset
Write tests with Pest's functional test()/expect() style and apply RefreshDatabase to feature tests through shared Pest configuration.

## Factory-backed test records
Create test-owned model records with model factories rather than inserting fixture rows manually.

## Input Data test factories
Build reusable domain input Data fixtures in Tests\Factory\Data classes exposing static make(array $overrides = []).
