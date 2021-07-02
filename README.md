

## How to install

Connect your db and run the following commands

1. >php artisan migrate
2. >php artisan db:seed --class=FriendStatusSeeder

## Serving the application

>php artisan serve

## Headers to add with every request

1. Accept : application/json
2. Authorization : Bearer "your received token"

example if your bearer token is "3|c2NBZ6dVRTYw0LJjqKm15MCEn0r5k0HLxIPTX5w2", please send
Bearer 3|c2NBZ6dVRTYw0LJjqKm15MCEn0r5k0HLxIPTX5w2 as your Authorization header


## API Documentation

Base EndPoint = **APP_URL**/api/
Where APP_URL is the url of the app.

1. Endpoint : /register
    METHOD : POST
    Mandatory fields  : name,password,email

    Response :
    status code : 200 - Successfully created account, 400 - if validation checks have failed
   
2. Endpoint : /login
   METHOD : POST
   Mandatory Fields : email,password

   Response : 200 - Login successful with token, 400 - Fields incorrect or not provided, 403 - Invalid Credentials (Wrong password provided)
    Data : auth token ( used to access the following endpoints)

3. Endpoint v /profile
    METHOD : GET

    Response : 200 
    Data :  profile_data, friends, pending_requests

4. Endpoint : /userprofile/{user_id}
    METHOD : GET
    mandatory field : user_id (inside the url)
    Response : 200 
    Data : user_data, isFriend, mutual_friends, has_sent_request_to_you

5. Endpoint : /sendfriendrequest
    METHOD : POST
    mandatory field : friend_id (any user id)

    Response :
    Status code : 200
    Data : Succesfully sent

    Errors : 400 - friend_id not provided or not a valid user_id
    401 : if one of the following cases :
    - You have already sent a request to this person
    - You are already friends with this person
    - This person has already sent a friend request to you

6. Endpoint : /searchuser/{search_param}
   METHOD : GET
   mandatory field : search_param (This can be either email or the name of the user)

   Response :
   Status code : 200
   Data : a list of users matching with the query

   Error : 400 - if no params are provided

7. Endpoint : /acceptrequest
   METHOD : POST
   mandatory field : friendship_id (this is the pending request id)

   Response :
   Status code : 200
   Data : validate whether this id is valid or not

   Status code : 400
   If already friends

   Status code : 403
   If this is a different user trying to accept a request of another person

