## Setup

### 1. Archivo .env.
> Asigna valor a tus variables de ambiente

### serverName
> Define el nombre para tu servidor acorde con la app que vas a usar

### port
> Define el puerto de salida de la app que vas a usar

### pathApp
> Define el path de tu proyecto que se va a incluir en el docker
```env
pathApp = './NombreDelRepo'
```
>Ejemplo

### passwordDB
> Contraseña para la base el user: sa de la DB 

### 2. Clona tu repositorio dentro de Docker-NGINX-PIS.
```
  git clone https://github.com/userName/repository.git
```

### 3. En tu terminal ejecuta el siguente comando dentro de docker-PIS.
```
  docker-compose up --build
```
> La siguiente vez que quieras correr el docker no es necesario agregar el '--build' en el comando.


## Xdebug
Si vas a usar xdebug te dejo un ejemplo en el archivo launch.Example.json
> Solo remplaza nameAppFolder por el nombre de la carpeta del repositorio que clonaste 
> Y renombra el archivo a launch.json

## Extenciones php
>En el archivo dockerfile, puedes agregar o quitar las extenciones que requieras
>Por default se agregan las minimas necesarias para conexion a BD y otras funciones de php

# Data Base SQL-Server
Por default en el archivo Docker-compose.yml el servicio de serverName_DB con un puerto y contraseña por default

## Para conectarte desde Terminal

>Tu docker debera de estar encendido

### 1.- Corre el siguiente comando para entrar en tu docker de la BD

docker exec -ti serverName_DB bash

>Si fue exitoso, en tu consola deberia aparecer: mssql@36460947b4d1:/$
>Para salir escribir exit

### 2.- Corre el siguiente comando para ingresar a la BD

/opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P 'password'

>Si fue exitoso, en tu consola deberia aparecer: 1>
>Para salir ctrl+C

### 3.- Como hacer las consultas a mi BD desde terminal

Se tendra que ingresar la consulta y luego la palabra "go"

1> select name from sys.databases
2> go

- - - - - - - - 

Tambien puedes devidir la consulta en rows

1> select name from
2> sys.databases
3> go

### 4.- Crear un usuario con permisos de administrador para conectarse desde Azure Data Studio con SQL login
Server: localhost
User name: sa
Password: La contraseña que definifiste en el archivo Docker-compose.yml

Database: Base de datos a la que quieras conectarte
>Si no te conectas a ninguna te conectara a master

### documentation 
>https://blog.logrocket.com/docker-sql-server/
>https://stackoverflow.com/questions/54519615/how-to-grant-db-owner-permissions-to-an-application-role
>https://github.com/Namoshek/docker-php-mssql

## Listo a trabajar.


#   A p p T S H B a c k  
 