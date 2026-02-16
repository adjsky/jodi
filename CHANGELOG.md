# Releases

## 0.15.2 (2026-02-16)

### Fix

- use dynamic configs for firebase

## 0.15.1 (2026-02-16)

### Fix

- pin node to 24.12.0

## 0.15.0 (2026-02-16)

### Feat

- native pushes (#77)

## 0.14.4 (2026-02-08)

### Fix

- android assets (#76)
- auth layout jumps

## 0.14.3 (2026-02-07)

### Fix

- take safe area into account

## 0.14.2 (2026-02-07)

### Fix

- increase padding a little bit

## 0.14.1 (2026-02-07)

### Fix

- tweak auth pages again
- push notifications checks & await listener (#74)

## 0.14.0 (2026-02-06)

### Feat

- support capacitor and handle go back properly

## 0.13.18 (2026-02-06)

### Fix

- tweak auth pages
- use p-safe instead

## 0.13.17 (2026-02-06)

### Fix

- add safe area to body

## 0.13.16 (2026-02-06)

### Fix

- swap translations

## 0.13.15 (2026-02-06)

### Fix

- todo notifications rewording

## 0.13.14 (2026-02-06)

### Fix

- remaining time in push notifications

## 0.13.13 (2026-02-06)

### Fix

- tweak safe area insets

## 0.13.12 (2026-02-06)

### Fix

- remove data-autofocus from categories back button

## 0.13.11 (2026-02-06)

### Fix

- update assetlinks

## 0.13.10 (2026-02-06)

### Fix

- add any to maskable icon
- revert maskable icon

## 0.13.9 (2026-02-06)

### Fix

- try to check if maskable icon is used in the splash screen

## 0.13.8 (2026-02-06)

### Fix

- add safe area inset to add todo/event buttons

## 0.13.7 (2026-02-06)

### Fix

- make maskable icon smaller

## 0.13.6 (2026-02-06)

### Fix

- assetslink

## 0.13.5 (2026-02-06)

### Fix

- add safe area insets to todo/event action bar & add button

## 0.13.4 (2026-02-06)

### Fix

- stabilize use-search-params

## 0.13.3 (2026-02-06)

### Fix

- add viewport-fit=cover

## 0.13.2 (2026-02-06)

### Fix

- use visits in year calendar preview & prevent data race when selecting todo/event date (#64)

## 0.13.1 (2026-02-05)

### Fix

- add assetlinks.json

## 0.13.0 (2026-02-05)

### Feat

- prefetch week days (#62)

### Fix

- todos time/notification bugs (#63)

## 0.12.2 (2026-02-04)

### Fix

- use database path from config
- remove unnecessary pdo_mysql extension

## 0.12.1 (2026-02-04)

### Fix

- try to build image without cache

## 0.12.0 (2026-02-04)

### Feat

- support notifications in todos (#60)
- support time in todos
- refactor category selection

### Fix

- make description scrollable
- create a new migration to update index

## 0.11.0 (2026-01-30)

### Feat

- move confirmable open state to url (history)

### Fix

- don't portal anything that renders inside a bottom sheet (i hope i won't make this mistake again)

## 0.10.0 (2026-01-28)

### Feat

- add vibration when selecting hours/minutes

### Fix

- todos reordering
- restore history modals
- don't create extra history entries on reload
- selected date resets
- render year calendar dialog in a portal
- broken checkbox
- apply border radius only for top
- use onInteractOutside instead of the focus one

## 0.9.2 (2026-01-25)

### Fix

- bump dnd-kit

## 0.9.1 (2026-01-25)

### Fix

- add missing name
- set fixed width for paragraph to prevent unnecessary wraps

## 0.9.0 (2026-01-25)

### Feat

- show configure push banner again after log out
- add optimistic updates to categories

### Fix

- use null instead of transparent
- move confirmable higher in the tree to prevent immediate closes

## 0.8.6 (2026-01-21)

### Fix

- apply changes on animation end
- ignore notify_at

## 0.8.5 (2026-01-21)

### Fix

- imask

## 0.8.4 (2026-01-21)

### Fix

- path to 72x72 icon

## 0.8.3 (2026-01-21)

### Fix

- check time before adding 1 hour
- use now() instead of starts_at when calculating remaining time

## 0.8.2 (2026-01-21)

### Fix

- prevent crashes when closing todo/event sheet

## 0.8.1 (2026-01-21)

### Fix

- prevent selection in time picker

## 0.8.0 (2026-01-21)

### Feat

- custom time picker

## 0.7.6 (2026-01-21)

### Fix

- revert 72x72 icon & badge

## 0.7.5 (2026-01-19)

### Fix

- hide instead of unmounting events and todos list
- handle empty categories as nulls

## 0.7.4 (2026-01-19)

## 0.7.3 (2026-01-18)

### Fix

- load categories when creating/updating todos
- normalize iso string when completing todos

## 0.7.2 (2026-01-18)

## 0.7.1 (2026-01-17)

### Fix

- render FloatingView in portal by default

## 0.7.0 (2026-01-17)

### Feat

- better time picker UX

### Fix

- don't render year calendar (dialog) in portal

## 0.6.3 (2026-01-14)

### Fix

- reveal progress before starting

## 0.6.2 (2026-01-13)

### Fix

- correctly redirect to notifications view
- render User.Avatar as div where needed
- update number of invitations on invite/delete

## 0.6.1 (2026-01-13)

### Fix

- increase font size in mails

## 0.6.0 (2026-01-13)

### Feat

- beautiful emails

### Fix

- svelte bug

## 0.5.5 (2026-01-12)

## 0.5.4 (2026-01-08)

### Fix

- use stable search string

## 0.5.3 (2026-01-08)

### Fix

- timezone mismatch between client and server
- category id casting
- revert notification icon

## 0.5.2 (2026-01-08)

## 0.5.1 (2026-01-07)

### Fix

- theme color mismatch

## 0.5.0 (2026-01-07)

### Feat

- show progress indicator in several cases

## 0.4.3 (2026-01-08)

## 0.4.2 (2026-01-08)

## 0.4.1 (2026-01-07)

### Fix

- todos->events typo
- properly handle push notifications

## 0.4.0 (2026-01-07)

### Feat

- go to current date/year

### Fix

- pass VAPID_PUBLIC_KEY at runtime

## 0.3.4 (2026-01-06)

### Fix

- don't overwrite db/env on every launch

## 0.3.3 (2026-01-06)

### Fix

- history api struggles in user settings
- log errors if subscribeToPushNotifications failed
- check for accidental snaps in the first place
- hanging deferred props loaders

## 0.3.2 (2026-01-06)

### Fix

- use built-in trusted proxy middleware

## 0.3.1 (2026-01-06)

### Fix

- move trust proxies middleware to a separate web middleware to prevent crashes

## 0.3.0 (2026-01-06)

### Feat

- add trustProxies middleware configuration

## 0.2.7 (2026-01-06)

## 0.2.6 (2026-01-06)

### Fix

- disable ensure_pages_exist configuration

## 0.2.5 (2026-01-06)

## 0.2.4 (2026-01-06)

### Fix

- use a different env variable to prevent ssl handshake issues

## 0.2.3 (2026-01-06)

## 0.2.2 (2026-01-05)

### Fix

- check keys presence before we generate them

## 0.2.1 (2026-01-06)

## 0.2.0 (2026-01-05)

## 0.1.2 (2026-01-05)

## 0.1.1 (2026-01-05)

## 0.1.0 (2026-01-04)
