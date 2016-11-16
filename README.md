# xbase
Center X Base Framework

# TODO

    TEST on each Model/Controll.
        - This is the easies way to test the code. It does not need to create test view pages nor ajax test.
    USER CRUD with PHOTO
    POST CRUD with PHOTO
    

# MC pattern

Since it is designed for backend database of web/app, there is no view.

So, there is only model and controller without view.

model.controller

index.php?mc=user.list
index.php?mc=user.register


# Database

When there is error, SQL error message will be return in JSON


ex)
    {"code":-1120,"message":"no such table: use2r - SELECT * FROM use2r"}



# TEST

    * All test file must be end with '_test.php'

    * All test file must have 'run()' method which will be invoked when "index.php?mc=test.all" has accessed.
    
    * A test method can be invoked indivisually like below
     
        * \app\php\php index.php "mc=test.method&method=user.user_crud_test.register&id=myid2&password=12345&email=abc@def.co"
        * \app\php\php index.php "mc=test.method&method=user.user_crud_test.update"
    
    * Or all test can be called like below.
    
        * \app\php\php index.php "mc=test.all"


# EXAMPLE CODES

    * see test files.
    