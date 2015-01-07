#ESG Application

View this code live:
  1. [admin.php](http://lucasem.scripts.mit.edu/esg/admin.php)
  2. [app.php](http://lucasem.scripts.mit.edu/esg/app.php)

- `admin.php`
  * Dependencies:
    1. `admin_util.php`
    2. `user_util.php`
  * This is the interface for administrators. It allows the creation of a new application and the updating of kerberii, neither of which can be done otherwise.
  * The changelog and associated records are viewed through this.
- `admin_util.php`
  * Administrator login, writes changes to the database, view for changelog
- `app.php`
  * Dependencies:
    1. `app_subjects.php`
    2. `app_util.php`
  * This is the interface for users. Here, a GET parameter `id` can be set to enter a certain application.
  * Alternatively, if the user has his/her MIT certificate and the associated kerberos is registered with the user's application, authentication can done without the application id.
- `app_subjects.php`
  * Organizes special formatting for subjects
- `app_util.php`
  * Dependencies:
    1. `admin_util.php`
  * Contains necessary utilities for viewing application and making user-changes.
- `changelog.csv`
  * Stores all changes made by users or administrators.
  * NEVER overwritten, only ever appended to.
  * Format:
    1. Time (YYYY-MM-DD-h-m-s)
    2. Type (save/submit/admin [admin kerb])
    3. User (id) (the user for which information was added)
    4. Diff (json string of changes)
- `esg.json`
  * With the exception of the `"administrators"` field, everything is for the sake of application fields and general application info.
  * This file is VITAL. Be very careful when making changes.
- `lottery.php`
  * Dependencies:
    1. `admin_util.php` (for login)
    2. `user_util.php` (for view)
  * Interface for lottery. Uses md5 hash on the seed and user id for sorting.
- `modify.php`
  * Dependencies:
    1. `admin_util.php`
    2. `modify_util.php`
  * Interface for modifying the application (`esg.json`)
- `modify_util.php`
  * Views for editing application
- `php.ini`
  * PHP dependencies
- `records.php`
  * Dependencies:
    1. `admin_util.php`
  * Interface for the `records` folder to view stored copies of `users.json`.
  * Should never be accessed directly. Used through `admin.php`.
- `user.php`
  * Dependencies:
    1. `admin_util.php`
    2. `user_util.php`
  * Interface for viewing information on a single user
- `user_util.php`
  * Views for all users (lottery and otherwise) as well as single users
- `users.json`
  * This file contains json which maps a user ID (formulated by a base64 encoding of 'lastfirst' name) to all of the user's fields.

