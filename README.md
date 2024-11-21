# Material Harbor

## Local Development

### Setup Environment
* Install XAMPP
    * https://www.apachefriends.org/
    * Either:
        * Delete files in xampp/htdocs folder and add project
            * Could also create shortcut to this folder
        * Change xampp apache config folder

### Run Server
* Run XAMPP Control Panel (xampp-control in xampp folder)
    * Start Apache Server
    * Start MySQL Server
* Visit localhost:8000 in web browser

## Database Environment Variables
* To connect to database, use environment variables in file `.env`:
* `.env` example:
```
DB_SERVER=localhost
DB_USER=root
DB_PASS=password
DB_NAME=material_harbor
```

## Server
* Directory
  * `/var/www/material_harbor`
