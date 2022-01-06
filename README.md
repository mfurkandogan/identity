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

You can check from this link on your localhost:

> http://127.0.0.1:8000/api/documentation



### Post - Login Request : /api/v1/login

```
Body Example : 
{
    "user"     :     "",        --required (user name or email)
    "password"  :    ""         --required (clear text)
}
```

### Post - Register Request : /api/v1/register
```
Body Example : 
{
    "name":"",          --required
    "email":"",         --required
    "password":""       --required(clear text)
}
```


