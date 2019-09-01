# horse-racing-service
Implementig a horse racing simulator with PHP and a relational database.

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

* Maker migration to create database and tables. 

  `php artisan migrate`

* DB Seed to generate content for horses table randomly

  `php artisan db:seed --class=HorsesTableSeeder`

## Hints

There are 4 important folders whiche have include implementations in application layer
* Http Requests

  `backend/app/Http/Requests`

* Controllers 

  `backend/app/Http/Controllers`

* Repostories

  `backend/app/Repositories`

* Routes 

  `backend/routes`

There are 3 important folders in database layer under the `backend/database` folder 

  * factories
  * migrations
  * seeds





```
