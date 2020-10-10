TEST API NETWIZZY
=================

1- Configuración del servidor
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

POr temas de rendimiento y optimización instalamos las siguientes librerías

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
