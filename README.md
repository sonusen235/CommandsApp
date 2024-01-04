# li₃ 

##Installation Process

1. Download the Zip file/github url

2. Place the project in respective folder(We are using windows machine with xampp software)

3. I have taken the li3 library from below path.

   https://github.com/UnionOfRAD/lithium/tree/v2.0.0

4. Place the lithium library files in root folder inside 'libaries' folder.

5. Please install PHP8.1/PHP8.2 version. (We have used PHP8.1 version)

6. Install the flash library that is available in the project root folder as flash.zip, Unzip that file and place the flash libary folder inside 'libraries' folder.

7. Setup the database, the database sql export file is kept inside Database folder, create a new database with the name as "database".

8. Run the project in localhost.



##Issues/Observations

1. We are trying to render the flash message into a view page, but getting issue while rendering the flash library messages into view page. We have writen a sample Flash::output() fuction in views/pages/home.html.php line no 248

We are getting below 

lithium\template\TemplateException (code 500)
Undefined rendering step 'flash'.

Note:
Previously, we are having issue while reading the flash messages, we have done changes in the session adapter file from line no 259 to 262 in below file path, after that we are able to read the flash messages. but unable to render them in view page.

app/extensions/adapter/storage/session/model.php
