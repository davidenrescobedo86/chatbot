# Chatbot app

## Small review
The project has been developed using the Laravel and Angular frameworks. Laravel for the RESTful API and Angular for the frontend of the application.
I tried to develop all the tasks and due to time constraints perhaps some are not fully completed.

## Database
I used three tables in our database: user, account and transactions. 
To mount the database, there is the script: database.sql, inside the Laravel project in the directory: api-rest-laravel, right at the root.
Just in case, the database connection configuration in the .env file is configured:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_chat
DB_USERNAME=root
DB_PASSWORD=null

## Run the app
At least as I worked, the whole project is inside the directory: chatbot.
Run or put the entire project inside a web server, in this way we will already have the api running, as long as the server is running.
And to run the Angular project, you could use a console and being inside the "chatbot-angular" directory, execute the command: npm start. Please, don't run ng serve because I prevent something by running the npm start command.

By accessing the url that the npm start command launches, we can create a user and log in and interact.
