I have analyzed the code for both Activity and Room booking pages.

**The Issue:**
Currently, both pages start with a "Select Activity" or "Select Room Type" placeholder (value is empty). Because no actual item is selected by default, the price calculation function (`updatePrice`) correctly returns `$0.00` based on the empty selection.

**The Fix:**
I will modify the JavaScript initialization in both files to automatically select the **first available option** if no specific option was requested via the URL. This ensures that when the page loads:
1.  A valid Activity/Room Type is selected automatically.
2.  The price and total calculation runs immediately using this default selection.

**Plan of Action:**

1.  **Edit `resources/views/booking/activities/create.blade.php`:**
    *   Modify the JavaScript `DOMContentLoaded` event.
    *   In the initialization logic, if no activity is requested via URL, programmatically select the first available activity from the dropdown (skipping the "Select Activity" placeholder).
    *   Trigger `updatePrice()` immediately to calculate and display the costs.

2.  **Edit `resources/views/booking/rooms/create.blade.php`:**
    *   Apply the same logic: If no `room_type` is requested, select the first available Room Type.
    *   Trigger `populateRooms()` immediately to load the rooms and calculate the price.

This directly addresses your requirement for dynamic prices to be visible immediately upon page load instead of showing `$0.00`.