## Academia 750 API (BACKEND)

Academia 750 is an organization for the comprehensive preparation of oppositions to
Firefighters from the different Autonomous Communities of Spain.

It currently has social networks for the dissemination of its services and does not have
with a web presence as an official means of communication, likewise, it does not have
with a platform for the training of its students for the modalities
face-to-face and online

### User Credentials

Running the seeder will create
`Admin` Role 
`Student` Role (with no extra permissions)

`Default User` (Generated by seed)

* **Admin**
    * DNI: ***00000000T***
    * Password: ***academia750***

* **Question Claims**: This is the user that will get emails notifications of user claims.
    * DNI: ***00000001R***
    * Password: ***academia750***


## SET UP


### Install the project

- Get PHP 8.2 
- Get Composer 2
- Get Node v16.20.1 and Yarn

`composer install` 

If something fails install the php required extensions.

### ENV

- **APP_** Laravel APP env variables. Im not fully sure how much are they bieng used.
- **DB_**: Database connection env. Note that for testing we use memory sql.
- **CLOUDINARY_URL** URL to upload the files. Used on Materials API.
- **MAIL_** Env to config the email notifications. We have several notifications
 - Reset password
 - Lesson Activated
- **APP_MAIL_ADDRESS** The `from` for the email notifications
- **APP_MAIL_ADDRESS_IMPUGNACIONES** The seeder will create special user with this emails where all the test impugnations will be sent to
- **LOG_** Laravel Log Env variables

Other ENV variables we need to verify if are being used or are just legacy debt.


### Set Up the database

:warning: Is **STRICTLY FORBIDDEN** modify the database directly. We will always add a new migration.
The automated actions will take care of deploy the change in other user local environments, testing, staging and production environments.

1 - Install MYSQL

2 - Create the tables for the database (and test database if required)

3 - Create a user and grand him access 

```
mysql -u root
CREATE DATABASE bomberosapimysql;
CREATE USER 'bomberos'@'localhost' IDENTIFIED WITH mysql_native_password BY '123456';
GRANT ALL ON `bomberosapimysql`.* TO `bomberos`@`localhost`;
```


3 - Run the migrations 

> php artisan migrate   

4 - Run the Seeder (To get the super admin access as well as the roles permissions)

> php artisan db:seed

In order to create new migrations can follow laravel normal proceedment.
In order to update procedures can follow [update procedure tutorial](https://www.notion.so/tianlu/How-to-update-a-Procedure-5e0ca197145c4e859223fb4d0fb5131d?pvs=4) on our wiki.



## Run locally

Run the projects (The logs will be pipe on the console)
> yarn serve 

Generate the docs locally

> yarn docs

Run the test cases

> yarn test
> yarn test --filter name_of_test_case

For visual studio you can also use this plugin
Name: PHPUnit

VS Marketplace Link: https://marketplace.visualstudio.com/items?itemName=emallin.phpunit

### Deployment

We are using github pipe lines.

Note: Installing in a new server may require to update the filter


`.githubs/workflow/staging` -> Is pointing to Tianlu Server 
`.githubs/workflow/production` -> Is pointing to Production Server 

* ENV: 
SSH_PORT => Staging uses default, this is used for production
SSH_PRIVATE_KEY => For Staging server
SSH_PRODUCTION_KEY => Private key for production Server
SSH_USER => For production server (Staging uses default)

* Variables
FOLDER_NAME => For production server
PRODUCTION_SERVER =>  academia750.es
STAGING_SERVER => IP of the staging server

Secrets are storing in this URL (You need to request the permissions)

[Staging](https://www.notion.so/tianlu/Academy-750-Staging-dab91f712f4d4b4088f0edb656c5dde0?pvs=4)
[Production](https://www.notion.so/tianlu/Academy-750-Producci-n-d4a8e1de43b74fe3b9f1067cd3d74523?pvs=4)

## Server Installation

[Notion Guide](https://www.notion.so/tianlu/Academy-750-Code-Base-5833b818639448cea5607f6a7fa86ee5?pvs=4)


## Stack

- [Laravel 9](https://laravel.com/docs/9.x)
- [Database Eloquent](https://laravel.com/docs/9.x/eloquent)
- [Permissions Spatie](https://spatie.be/docs/laravel-permission/v5/introduction)
- [FPDF For PDF generation](https://github.com/Setasign/FPDF)
- [Error handling](https://sentry.io/)


## Integrations

### Api Documentation

We are using  [Scribe](https://scribe.knuckles.wtf/laravel/). 
Scribe does most of the job we take care of:

1 - Requests: Add the bodyParameters or the queryParameters function.

2 - Resources: Add the Resource/ResourceCollection response to allow scribe deduce the autoput.

3 - In the controller add (if required) the @authenticated, @urlParam for url parameters and other HTTP status scenarios.

4 - The models are taken from the Factories. Using create from another factory inside our factory wont work for doc, you can add 
` config('app.env') === 'documentation'` check for this specific scenarios.

Update the documentation running `APP_ENV=documentation php artisan scribe:generate php artisan scribe:generate` or `yarn doc`

You can open the `/docs`


### Sentry

If you have PHP 8.1.0 and you can not do

`php artisan sentry:test` 

getting the error

```
There was an error sending the event.
SDK: Failed to send the event to Sentry. Reason: "HTTP/2 stream 1 was reset for "https://o4505596447162368.ingest.sentry.io/api/4505596448210944/store/".".
Please check the error message from the SDK above for further hints about what went wrong.
```

Is due a problem with PHP CURL with sentry, you can reinstall sentry this way

composer update -W sentry/sentry-laravel

### Queues

Running queues we use `php artisan queue:work`.

For production we use SuperVisor (See the Server installation guide in Notion )

This functionality are using QUEUEs

- Send emails. (Forgot Password)
- Import CVS for topics, questions, etc.


### Schedules 

Running queues we use `php artisan schedule:work`, you can verify with `php artisan schedule:list`
and test with `php artisan schedule:test`

For production we use SuperVisor (See the Server installation guide in Notion )

This functionality are using Schedules

- Clear temporal files. (For example Student personalized PDFs)

### Permissions

When we generate a empty database we will start with the next setup:

Roles:
 1 - admin (can do everything)
 2 - student (empty role by default)

Entering as an admin we can create new roles with some permissions and create students to this roles.

The admin can NOT create new permissions, we handle the permissions from `PermissionSeeder.php` class.
You can **create or update permissions** in the seeder and run 

php artisan db:seed --class=PermissionSeeder

To make the update take effect. In production and staging environment this is done automatically.

In order to **delete** permissions you will need to create a migration.



## Architecture

This project has done through 2 phases, in which the architecture followed is different, trying to simplify the process in phase 2.

Giving a new model and controller you need, for example `Cars`

- Migration: We will probably start adding a migration to add or edit the cars table.

- Seeder: We will use seeder when we need to init the database after a new installation

- Model File `Car`: With database information, specific business logic of this model, relations between model (many to many, belongs to).

- Factory `CarFactory`: We will use a factory that helps us to create data in our test cases.

- Routes File:  a `car.routes` with the end points related to Cars. Include it in the `routes/api/v1/index.php` file (where it has some global middleware). The routes file has the route, the method, and some extra middelware.

- Request File: For each method that requires, use `model + action + 'Request'` pattern as `CarCreateRequest.php` or `CarListRequest.php`. It contains the validation (422) of the query, the documentation of the parameters (`queryParameters` or `bodyParameters` function), and the permission check when is a permission based security (Defined in `PermissionSeeder.php`)

- Controller File `CarController`:  The controller file contains the methods it self (in the phase 1 it was a facade but now we simplify). It requires to follow: Documentation blog, a try catch covering the function, extra checks, action to the database (modify or retrieve a value)

Basic Envelope

``````php

   try {

        // API HERE
        return response()->json([
            'status' => 'successfully',
            'result' => $data
        ], 200);

    } catch (\Exception $err) {
        // Log the error in sentry and in our log
        Log::error($err->getMessage());
        // Return the error to the client
        return response()->json([
            'status' => 'error',
            'error' => $err->getMessage()
        ], 500);
    }
``````
    
Most common actions are:

- `List`: List of items with pagination, order-by, general search and specific filters. Also my return some counts of belongs to items.
- `Calendar`: Similar to the list but focus on time line, there is no pagination but we search using from-to dates, and there is a maximum date range that we can search on.
- `Search`: Search api by content without exposing any private data (for example search for users in a auto complete form)
- `Create`: Create a new car, use the minimum parameters and those who can not change after creation.
- `Edit`: Edit car properties, normally most parameters are allowed (except restricted by business logic) and ALL are optional.
- `Info`: Returns the info for a single car.
- `Delete`: Deletes a car and all the information related to it.
- `Action`: Specific logics or business actions over the car.



- `CarTest`: Always do test cases! Rehuse the logic from previous test cases to see that you dont miss anything. Test first error cases generally (401 not allowed, 403 no permissions or forbidden, 404 not found, 422 wrong parameters, 409 conflict or duplicated) then move to the 200 scenarios (return the correct data, has the correct values, doesnt return not wanted data, different logics, relations with other databases)
    
- `Resource` I only use resource in the phase 2 to auto complete the object on the documentation, and each time less. In the phase 1 was heavily used for relations and meta information.

- `Notification` Use this type of folder to add new notifications to the users (app or email ones)



## Databases


### Meta information

- migrations: Laravel database migrations runned.
--- 
- Jobs: Laravel jobs (using database driver)
- failed_jobs: Jobs that had failed!
--- 
- images: used to store users avatars (polymorphic can have different uses)

### Users

- Users: List of users
--- 
- personal_access_tokens: User logged tokens
- passwords_resets: To rest the password

--- 
Permissions are handle by Spatie

- Roles: We have two main roles ADMIN and STUDENT. 
- Permissions: Specific platform permissions (Not in used but modified in the profile tasks to do)
- Roles_has_permissions: Relation betwen roles and permissions
- Model_has_roles: Relation between models (users or any other entity is polimorphic) and roles
- Model_has_permissions: Relation between models (users or any other entity is polimorphic) and permissions (without need of the role)

### Oppositions and Tests

* oppositions: List of oppositions (big exams for goverment job), a opposition has several topics and questions.
* opposition_user: Pivot table between users and oppositions. WARNING! Requires manual updated directly on the database
---
* topic_group: All opposition topics stay between 3 groups: Generic, Law, Specific.
* topic: A topic belongs to a group.
* sub_topics:   A subtopic belong to a topic
* oppositionable: Relates topics and subtopcis with the opposition table.

--- 
* questions: A question belong to a topic or sub topic. Can be for test or a memory card.
* answers: Is the possible answers to the questions - 4 for tests - 1 for memory card.
--- 
* tests_types: DEPRECATED Is 2 test types and is not in use
* tests: A student can generate a test with a number of questions. This is the list of tests by student.
* testables: Relates the tests with the selected topics/subtopics in that specific test
* questions_test: The questions selected for a specific test and the status (Unanswered, correct or fail)
--- 
* questions_used_test: A relation between user and question to keep track of all the questions
showed to a student and the results (Independent with the test)
--- 
- import_records: List of questions imported by CSV and the queue job. (Displayed on the notifications)
- import_process: Status of the CSV import (Pending, completed or failed)


### Lessons & Materials

- tags: A table with tags and type. We use tags in materials but can be expanded by other users.
---
- groups: The entity of group of students. A group has a name and a color.
- groups_users: The relation between groups and users. A user is active if the discharged date is NULL.
---
- workspaces: Material Categories, a high level of abstraction or 'folder' to organize materials.
- materials: Materials are linked to a URL where the PDF or video is stored.
---
- lesson: Classes where the student can join, could be on class or online.
- lesson_user: Users that are linked to a lesson (can be related to a group but there is no a hard connection with the group)
- lesson_material: Materials assigned to the lesson.


## Desarrolladores (Phase 1)

* ___Raúl Alberto Moheno Zavaleta___
* Adolfo Feria
* Carlos Herrera

## Desarrolladores (Phase 2)
* Abel Bordonado Lillo
