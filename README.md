TEST API NETWIZZY
=================

1- Objetivos de la prueba y consideraciones iniciales
-----------------------------------------------------

Los objetivos de la prueva es realir un servicion REST API en php sobre un servidor CentOS 7.
La API debe tener sólo un endpoint */servicetest/api.php* donde se mostrará un listado de los juegos con información
calculada a partir de todas las partidas de los mismos. Además podrá recibir un id como parámetro para filtrar esta información para un juego determinado.

Los datos del servidor deben obtenerse a su vez de otro REST API en otro servidor que consumiremos.

Para realizar esta tarea considero lo siguiente:

- Códido escalable y mantenible. Debería se fácil añadir nuevos servicios a la API REST
- Código robusto y testeable
- Con un buen performance para que las peticiones se sirvan los más rápido posible
- Subidas a producción sencillas y agiles con versionado de código

Para cumplir con estos objetivos se han realizado las tareas con el siguiente stack tecnológico.

1. Un pequeño microframework para utilizar las ventajas del patrón MVC y así conseguiremos y código más desacoplado, distintos entornos (desarrollo y producción), enrutamiento de los endpoints y otras ventajas.

2. PHPUnit como framework de testeo de código

3. Memcached como cacheo entre el servidor y los datos.

4. Para hacer el deploy en el servidor se utilizará un pull del repositorio del código alojado en una cuenta de GitHub



2- Configuración del servidor
-----------------------------

*Instalación de Apache*

```bash
sudo yum install httpd
```

Arrancamos el servicio
```bash
sudo systemctl start httpd.service
```


Activamos el servicio para que arranque en los reinicios
```bash
sudo systemctl enable httpd.service
``` 

Probamos que funciona el servidor web a través del navegador http://ip
Vemos que NO funciona :(

Vamos a comprobar que apache está escuchando en el puerto 80
```bash
netstat -tulpn | grep 80
```

Perfecto el servicio apache está escuchando y funcionando. Vamos a comprobar el firewall.

Habilitamos el tráfico web

sudo firewall-cmd --permanent --zone=public --add-service=http
sudo firewall-cmd --permanent --zone=public --add-service=https
sudo firewall-cmd --reload

Comprobamos ahora en el navegador y funciona perfectamente!

*Instalación de PHP*

```bash
sudo yum install php
```

Por temas de rendimiento y optimización instalamos las siguientes librerías

Instalamos Memcached para cachear las respuestas de las peticiones de la api

```bash
sudo yum install memcached
sudo yum install libmemcached
sudo yum install php-pecl-memcache
```

Vamos a configurar memcached para que sólo se pueda acceder desde el servidor local. Para eso editamos el fichero de configuración y establecemos los siguientes parámetros:

```bash
sudo vim /etc/sysconfig/memcached

PORT="11211"
USER="memcached"
MAXCONN="1024"
CACHESIZE="64"
OPTIONS="-l 127.0.0.1 -U 0"
```
Arrancamos el servicio y lo hacemos autoiniciable

```bash
sudo systemctl restart memcached
systemctl enable memcached
```
Abrimos el firewall para aceptar las peticiones por ese puerto

```bash
sudo firewall-cmd --permanent --zone=public --add-port=11211/tcp
```

Podemos consultar las estadísticas de memcached con el siguiente comando:

```bash
memcached-tool 127.0.0.1 stats
```
Instalamos opcache

```bash
sudo yum install php-opcache
```

Reiniciamos apache

```bash
sudo systemctl restart httpd.service
```
Instalamos git

```bash
sudo yum install git
```

Instalamos composer 

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '795f976fe0ebd8b75f26a6dd68f78fd3453ce79f32ecb33e7fd087d39bfeb978342fb73ac986cd4f54edd0dc902601dc') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer
```

Clonamos el proyecto en producción

```bash
cd /var/www
git clone https://github.com/adagber/api-test.git
```

Cuando se ha descagado el proyecto debemos instalar las dependencias ya en producción por lo que haremos la instalación optimizada y si librerías de desarrollo

```bash
composer install --no-dev --optimize-autoloader
```

Ahora creamos el virtualhost de Apache para que apunte al directorio correcto. Para ello usaremos la siguiente configuración:

```vhost.conf
Listen 8080

<VirtualHost *:8080>

    ServerName www.api-test.com
    ServerAlias api-test.com

    DocumentRoot /var/www/api-test/public
    DirectoryIndex /index.php

    ErrorLog /var/log/httpd/api-test_error.log
    CustomLog /var/log/httpd/api-test_access.log combined

    <Directory "/var/www/api-test/public">
        AllowOverride None
        Require all granted

        FallbackResource /index.php
    </Directory>

    SetEnv APP_ENV prod
</VirtualHost>
```

2- Configuración de la aplicación
---------------------------------

Para configurar la aplicación en los distintos entornos sólo hay que editar el fichero *.env* situado en la raín del proyecto.

Allí encontraremos las variables de entorno donde podremos especificar el entorno de ejecución (desarrollo o producción)
y es dónde pondremos las nuevas variable de entorno en caso de necesitarlas.

Para establecer las rutas solo hay que editar el fichero *scr/app.php*. Donde podemos definir el normbre de la ruta el controlador asociado y sus parámetros

3- Ejecución de los test
------------------------

Parte de la aplicación está testeada. Los test se encuentran en la carpeta *tests* donde se pueden ir añadiendo según crezca el proyecto.

Para ejecutarlos sólo hay que ejecutar lo siguiente:

```bash
php vendor/bin/phpunit
```

