# Rezise Image

### Redimensionar imagenes con PHP "ease-to-use"

Código utilizado para optimizar las imágenes en una página con diseño
responsivo. Y así no modificar una por una. Esto hace una versión más
pequeña de una imagen original. Con las dimensiones como parámetros
ajustables.

El código es estructurado y muy simple. Queda en cada uno mejorarlo a las necesidades de ustedes.

------------



Ajustes:

    const _x1 = 400;
    const _x2 = 800;
    const _x3 = 1400;

Por defecto vienen 3 resoluciones en el código, pueden ajustar las resoluciones con los parámetros mostrados anteriormente. 

En el caso de que quieran añadir o quitar resoluciones, deberán hacerlo manualmente quitando los IF correspondientes.

```php
if ($width <= _x1) { //Si es menor a 400px de ancho
    //Si cae aquí, directamente se guardará en la carpeta de "small"
} elseif ($width > _x1 && $width <= _x2) { //Si es mayor a 400px y menor o igual a 800px de ancho
    //Si cae aquí, se guardará en la carpeta "medium",
    //pero también se creará una copia de la imagen con un tamaño de 400px de ancho
} elseif ($width > _x2 && $width <= _x3) { //Si es mayor a 800px y menor o igual a 1400px de ancho
    //Si cae aquí, se guardará en la carpeta "large",
    //pero también se creará una copia de la imagen con un tamaño de 800px de ancho y otra de 400px.
} else {//Los que son mayores a 1400px de ancho
    //Si cae aquí, se generará una copia en cada carpeta.
}
```
