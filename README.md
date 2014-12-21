#ESG Application

- `admin.php`
  * This is the interface for administrators. It allows the creation of a new application and the updating of kerberii, neither of which can be done otherwise.
  * The changelog and associated records are viewed through this.
- `app.php`
  * Dependencies:
    1. `app_subjects.php`
      * Organizes special formatting for subjects
    2. `app_util.php`
      * Lots of necessary utilities
  * This is the interface for users. Here, a GET parameter `id` can be set to enter a certain application.
  * Alternatively, if the user has his/her MIT certificate and the associated kerberos is registered with the user's application, authentication can done without the application id.
- `changelog.csv`
  * Stores all changes made by users or administrators.
  * NEVER overwritten, only ever appended to.
  * Format:
    1. Time (YYYY-MM-DD-h-m-s)
    2. Type (save/submit/admin [admin kerb])
    3. User (id) (the user for which information was added)
    4. Diff (json string of changes)
- `esg.json`
  * With the exception of the `"administrators"` field, everything is for the sake of application fields and general application info. This file is VITAL.
  * NEVER written to. Manual changes only.
- `php.ini`
  * PHP dependencies
- `records.php`
  * Interface for the `records` folder to view stored copies of `users.json`.
  * Should never be accessed directly. Used through `admin.php`.
- `users.json`
  * This file contains json which maps a user ID (formulated by a base64 encoding of 'lastfirst' name) to all of the user's fields.

