<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Academia 750 API (BACKEND)

Academia 750 es una organización de preparación integral de las oposiciones a
bomberos de las diferentes Comunidades Autónomas de España.
Actualmente cuenta con redes sociales para la difusión de sus servicios y no cuenta
con una presencia web como medio oficial de comunicación, así mismo, no cuenta
con una plataforma para la formación de sus alumnos para las modalidades
presencial y online

## Desarrolladores
* ___Raúl Alberto Moheno Zavaleta___
* Adolfo Feria
* Carlos Herrera

### Credenciales de acceso para pruebas (Solo funcionará en Pre-Producción)

* ```Carlos Herrera```
  * **Admin**
    * DNI: ***94344041L***
    * Password: ***54P%$VviB***
  * **Alumno**
    * DNI: ***41426213Q***
    * Password: ***xF7@EGm$UZ3***
  

* ```Ginés Rabasco```
  * **Admin**
    * DNI: ***74237694L***
    * Password: ***academia750***
  * **Alumno**
    * DNI: ***10668102N***
    * Password: ***academia750***
  

* ```Adolfo Feria```
  * **Admin**
    * DNI: ***42711006Y***
    * Password: ***@UX!M54wQn***
  * **Alumno**
    * DNI: ***67239172Y***
    * Password: ***zKY$MUM3KWRn9#***
    

* ```Raúl Moheno```
  * **Admin**
    * DNI: ***32631674X***
    * Password: ***g5UZXCHJ5Zm#AB!***
  * **Alumno**
    * DNI: ***14071663X***
    * Password: ***EcsN9HYA9)&***


### Troubleshooting 

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
