# xbase
Center X Base Framework

# TODO

    TEST on each Model/Controll.
        - This is the easies way to test the code. It does not need to create test view pages nor ajax test.
    USER CRUD with PHOTO
    POST CRUD with PHOTO
    
    TEST on PERMISSION for security.
    
        - users can crud only on their post, profile.
            if it's not user's data, block it.
            like
            -- post_config crud
            -- 
        - admin can do what ever.
    
    @note FORUM cannot be deleted - if you do, there are many works.
    
# Installation

    copy var/db/xbase.db to var/db/database.db
    give permission on var/db
    give permission on va/db/database.db
    

# MC pattern

Since it is designed for backend database of web/app, there is no view.

So, there is only model and controller without view.

model.controller

index.php?mc=user.list
index.php?mc=user.register


# Database

* var/db/xbase.db is the template database. Do not overrwite this file.
    This file is added into git.
    Copy this file into something like "database.php" and use the copied file as database.

    * @Attention file extension is '.php' for security reason.
    

When there is error, SQL error message will be return in JSON


ex)
    {"code":-1120,"message":"no such table: use2r - SELECT * FROM use2r"}



# TEST

    * All test file must be end with '_test.php'

    * All test file must have 'run()' method which will be invoked when "index.php?mc=test.all" has accessed.
    
    * A test method can be invoked indivisually like below
     
        * \app\php\php index.php "mc=test.method&method=user.user_crud_test.register&id=myid2&password=12345&email=abc@def.co"
        * \app\php\php index.php "mc=test.method&method=user.user_crud_test.update"
        
    * Or you can invoke a test script
    
        * C:\app\php\php.exe .\index.php "mc=test.method&method=post.post_test.run
    
    * Or all test can be called like below.
    
        * \app\php\php index.php "mc=test.all"


# EXAMPLE CODES

    * see test files.



# Documentation

## User

all user are signed in.

even anonymous is singed in automatically.

anonyous user who did not signed in will use 'anonymous' account.






# TABLES

## user table


* user.primary_photo
    holds user's primary photo information.
    it is up to developer's choice how it would be.
    It can be firebase storage url
    or It can be a photo number
    or it can be relative file path.
    
    You can even save JSON string in it so that you can have many info.
    
    
