# Pasos a la hora de crear el proyecto

Voy a apuntar cosas importantes en este archivo. 

## Requisitos previos

1. Necesitamos tener instalado:
Symfony CLI, comoposer, XAMMP, Php.
2. Start XAMMP: MySQL y Apache Web Server para que la base de datos interactue con symfony a la hora de ir creando
esta y poder verlo en localhost/phpmyadmin

## Crear proyecto

Utilizamos el siguiente comando para crear el proyecto:
- Para web completas: `symfony new --webapp my_project`
- Para microservicios, API o aplicación de consola: `symfony new my_project`

## Clonar proyecto

Si lo que queremos es clonar un proyecto de github:
1. `git clone...`
2. `composer install` para instalar las dependencias necesarias

## Server start

Arrancamos el proyecto para verlo en el navegador con:
`symfony server:start`

## Archivo .env

Configuramos el archivo .env para conectar con la base de datos arreglo a lo que necesitemos.

## Entidades

Creamos las entidades(tablas) y los campos de estas. 
`symfonty console make:entity Entidad`

Una vez creadas las tablas establecemos las relaciones de estas con el mismo comando anterior
seleccionando OneToMany, ManyToOne o lo que necesitemos.

Todo se hace desde el ORM de symfony doctrine.

## Controladores

Para crear un controlador: `symfonty console make:controller`
Se creará un controlador un template .twig el cual renderiza este controlador. 

## Métodos mágicos - EntityManager

Los utilizamos para mostrar la info de la base de datos en los html.
Están reflejados en PostController.
Estos métodos los traemos de PostRepository, de la clase extendida ServiceEntityRepository.

## Repositorios

Son los métodos que usamos para traer los datos de la base de datos. 
Normalmente tenemos ya unos métodos definidos. Como los métodos anteriores. Pero podemos crear nuestros propios métodos personalizados.

## CRUD

Los métodos del controlador no sólo sirven para crear información. También
los usaremos para crear, leer, editar y eliminar datos. 
Ej: Post:
En PostController instanciamos la clase Post en un objeto pasándole los 
parámetros que pediremos en el constructor(Post.php) y utilizando el método flush(). Este
debe estar prensente en todas las operaciones. 

## FORM

Para crear un formulario utilizamos el comando: `symfony console make:form`
Nos preguntará para vincular con la entidad que vamos a utilizar el formulario.
Esto nos crea el una archivo LoqueseaType.php con los campos del formulario que serán los de la entidad(tabla)
que queremos usar. 

## LOGIN y LOGOUT

Siguiendo la documentación de Symfony en el apartado Seguridad nos dice que creemos un controlador con:
`symfony console make:controller Login`

En el archivo config/packages/security.yaml en `main:` agregamos lo referente al login( form_login - vease en el archivo ).

Para el logout añadimos la ruta /logout con la función correspondiente(vease el archivo). Y añadimos en security.yaml
lo que concierne a tal efecto. Sólo hay que ir a mirar el archivo para saberlo. 
