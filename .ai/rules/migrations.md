---
paths:
  - 'database/migrations/**'
---

# Migrations

## Constrain notification status in DB and PHP
Store notification status in database enum columns and cast matching model attributes to ReminderDeliveryStatus.
