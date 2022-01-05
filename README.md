In this project , Users may be able to login the application using either a username, or an email address and a password.


## Step 1: Create database

First you need to create a table called "identity_db".

Default settings :

DB_DATABASE=identity_db
<br>
DB_USERNAME=root
<br>
DB_PASSWORD=

## Step 2: Step up a database tables

Run this command to add the tables to the database

```
$ php artisan migrate
```

## Step 3: Final Step
Finally, run the command via composer:

```
php artisan serve
```

## User Endpoints

### Post - Login Request : /api/v1/login

```
Body Example : 
{
    "email"     :    "",        --required
    "password"  :    ""         --required
}
```

### Get - User Request : /api/v1/user
```
Header Example : 
{
    Bearer Token        --required
}
```

### Post - Register Request : /api/v1/register
```
Body Example : 
{
    "name":"",          --required
    "email":"",         --required
    "password":""       --required
}
```


### Post - Logout Request : /api/v1/logout
```
Header Example : 
{
    Bearer Token        --required
}
```


