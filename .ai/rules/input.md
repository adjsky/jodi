---
paths:
  - 'app/Domain/*/{Actions,Data/Input}/**'
---

# Input

## Validate action input with Data objects
Convert HTTP input into domain input Data objects and keep validation rules on those Data classes. Do not introduce Form Requests for domain actions.
