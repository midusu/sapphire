## Fix Activity Booking Layouts

The error accessing `/booking/activities` is caused by the views extending `layouts.app` (the authenticated admin/dashboard layout) instead of `layouts.public` (the public website layout). `layouts.app` likely expects authenticated user data or specific variables that aren't present on public pages.

### Implementation Steps:

1.  **Update Activity Index View**
    -   Modify `resources/views/booking/activities/index.blade.php`
    -   Change `@extends('layouts.app')` to `@extends('layouts.public')`

2.  **Update Activity Create View**
    -   Modify `resources/views/booking/activities/create.blade.php`
    -   Change `@extends('layouts.app')` to `@extends('layouts.public')`

This will ensure the public activity booking pages render correctly using the public site template.