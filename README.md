# horse-racing-service
Implementig a horse racing simulator API with PHP and a relational database.

**Technologies used**
- Docker
- PHP 7.2
- Laravel Framework 5.8
- Mysql 5.7
- Nginx 

## Installation
* Clone the repository 

  `https://github.com/umutbariskarasar/horse-racing-service.git`

* Docker Compose Build and Up

  `docker-compose up`

* Run and enter the container 

  `docker exec -it fpm bash`

* Make migration to create database and tables. 

  `php artisan migrate`

* DB Seed to generate content for horses table randomly

  `php artisan db:seed --class=HorsesTableSeeder`
  
   This command create 24 horses for each running. Because we need minimum 24 horses for 3 races. 


## Hints

There are 4 important folders which have include implementations in application layer
* Http Requests

  `backend/app/Http/Requests`

* Controllers 

  `backend/app/Http/Controllers`

* Repositories

  `backend/app/Repositories`
  

* Services

  `backend/app/Services`

* Routes 

  `backend/routes`
 
 ## Api Endpoints


| Method | URL               | 
| -------|-------------------|
| GET    | api/all-races/    |
| GET    | api/horses/{id}   |
| GET    | api/races/{id}    |
| GET    | api/all-races/    | 
| GET    | api/active-races/ | 
| GET    | api/races/{id}    | 
|**GET**| **api/progress/**  |  
| POST   | api/horses/       |
|**POST**| **api/races/**    |  
| PUT    | api/horses/       |
| PUT    | api/races/        |
| DELETE | api/horses/{id}   |
| DELETE | api/races/{id}    |


There are 3 important folders in database layer under the `backend/database` folder 

  * factories
  * migrations
  * seeds
  
  ## DB Schema
  ![db_schema](https://github.com/umutbariskarasar/horse-racing-service/blob/master/horse-race-schema.png)



```
