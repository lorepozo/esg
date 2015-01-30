#ESG Application

View this code live:
  1. [admin.php](http://lucasem.scripts.mit.edu/esg/admin.php)
  2. [app.php](http://lucasem.scripts.mit.edu/esg/app.php)


## File Overview

- `esg_records/*`
  * Stores old versions of `.esg` files.
- `images/*`
  * Stores images associated with each applicant
- `resources/*`
  * HTTP resources (such as javascript files, stylesheets, logos)
- `users/*`
  * Used via `parser.py` and `db.php`
  * This folder contains _year_ folders, which are full of `*.user` files (the latest, as well as old copies for any set of changes on a user)
- `add.php`
  * Dependencies:
    1. `admin_util.php`
    2. `db.php`
  * This is a minimal interface for adding a user. Intended for use at the end of an information session.
- `admin.php`
  * Dependencies:
    1. `admin_util.php`
    2. `db.php`
  * This is the interface for administrators. It allows the creation of a new application and the updating of kerberii, neither of which can be done otherwise.
  * The changelog and associated records are viewed through this.
- `admin_util.php`
  * Dependencies:
    1. `db.php`
    2. `user_util.php`
  * Administrator login, writes changes to the database, view for changelog
- `app.php`
  * Dependencies:
    1. `app_subjects.php`
    2. `app_util.php`
  * This is the interface for users. When used (via another file) a GET parameter `id` can be set to specify the applicant.
  * Alternatively, if the user has his/her MIT certificate and the associated kerberos is registered with the user's application, authentication can done without the application id.
- `app_subjects.php`
  * Organizes special formatting for subjects
- `app_util.php`
  * Dependencies:
    1. `admin_util.php`
    2. `db.php`
  * Contains necessary utilities for viewing application and making user-changes.
- `changelog.csv`
  * Stores all changes made by users or administrators.
  * NEVER overwritten, only ever appended to.
  * Format:
    1. Time (YYYY-MM-DD-h-m-s)
    2. Type (save/submit/admin [admin kerb])
    3. User (id) (the user for which information was added)
    4. Diff (json string of changes)
- `comm.php`
  * Dependencies:
    1. `db.php`
  * Handles all email communication.
- `db.php`
  * Dependencies:
    1. `parser.py`
  * This is the sole interface between the application system and actual stored data.
  * Any read/write operations must be done via this file.
- `globals.esg`
  * Used via `parser.py` and `db.php`
  * Specifies universal constants that aren't application-dependant.
  * This file is VITAL. Be very careful when making changes.
- `lottery.php`
  * Dependencies:
    1. `admin_util.php`
    2. `db.php`
    3. `user_util.php`
  * Interface for lottery. Uses md5 hash on the seed and user id for sorting.
- `parser.py`
  * Used via `db.php`
  * Parses `*.user` files and the various `.esg` files into json objects
  * Parses user json object into `*.user` file
- `php.ini`
  * PHP universal dependencies
- `user.php`
  * Dependencies:
    1. `admin_util.php`
    2. `db.php`
    3. `user_util.php`
  * Interface for viewing information on a single user
- `user_util.php`
  * Views for all users (lottery and otherwise) as well as single users
- These files are for particular application instances:
  * `spring_a.esg`, `spring_a.php`
  * `spring_b.esg`, `spring_b.php`
  * `fall.esg`, `fall.php`

## `.user` files
The layout of a `.user` file is simple: each line should match the format `key value`
where __key__ is an application field id (such as `mitid` or `esgphysicsradio`) which must NOT have any spaces,
and __value__ is the associated value (which can have spaces).

If you'd like some values to be empty, simply use the keyword `VOID`.

If you'd like to use newlines in the value, use the literal `%N%`

Comment lines must have the first non-whitespace character be the hash (#).

## `.esg` files
The `.esg` files have the same format as a `.user` file, but with some added keywords:

**NOTE**: as with `.user` files, newlines are written with `%N%`.

### DICT
```
DICT key
  key1 value1
  key2 value2
  ...
END
```

Can only exist inside another DICT (or the root).

The keys must not have any spaces. This is the same structure as the `esg` file itself, containing key/value pairs as well as the special keyword groups listed in this section.

In the `emailadd`, `emailsave`, and `emailsubmit` keys, you may use the keyword `%APPLINK%`, or any user field (e.g. `first` or `email`) with the syntax:

`%FIRST%` or `%FIRST|Alternative text%` (if the 'first' field isn't set for the user)

Note that the field name is *capitalized*, and a pipe `|` is used to separate the field and the alternative text.

### LIST
```
LIST key
  some item
  another
  ...
END
```

can only exist inside a DICT (or the root).

note that `key` is the key of the dictionary that the file is parsed into.

### SUBJECTS
```
SUBJECTS
  id Description
    OPTIONS
      ...
    END
  END
  differentid Different subject description
    OPTIONS
      ...
    END
  END
  ...
END
```

can only exist once inside the root.

`id` cannot have any spaces. OPTIONS usage described in the RADIO field below.

### FIELDS
```
FIELDS
  [FIELD_TYPE] id
    ...
  END
  ...
END
```

can only exist inside a DICT (or the root).

`[FIELD_TYPE]` can be one of TEXT, TEXTAREA, RADIO, or IMAGE (described below).

### FIELD TYPES

All of these types have the PROMPT parameter, and an option HELPTEXT parameter.

#### TEXT

No extra parameters. Usage:

```
TEXT mitid
  PROMPT MIT identification number
  HELPTEXT This can be found on the bottom-left of your MIT ID.
END
```

#### TEXTAREA

ROWS parameter. Usage:

```
TEXTAREA homeaddress
  PROMPT Home address
  ROWS 3
END
```

#### RADIO

OPTIONS parameter, used as a flat DICT (much like a `.user` file). Usage:

```
RADIO coursepreference
  PROMPT What course do you think you'll major in?
  OPTIONS
    six_three 6-3 (Computer Science and Engineering)
    eight 8 (Physics)
    eighteen 18 (Mathematics)
    other Other
  END
END
```

#### IMAGE

No extra parameters. Usage:

```
IMAGE photo
  PROMPT Please upload a photo of yourself
  HELPTEXT This is completely optional. You don't <emph>have</emph> to upload a photo.
END
```