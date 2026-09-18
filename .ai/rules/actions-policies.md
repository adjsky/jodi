---
paths:
  - 'api/app/Domain/*/{Actions,Policies}/**'
---

# Actions Policies

## Authorize actions through domain policies
Co-locate policies with their domain and implement controller-action authorization through authorize() and the authenticated user's can() method.
