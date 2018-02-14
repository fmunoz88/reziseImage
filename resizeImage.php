<?php

    const _x1 = 400;
    const _x2 = 800;
    const _x3 = 1400;
    
    $width = 0;
    $height = 0;
    
    $count = count($_FILES['imagen']['name']);

    //Se recorre en base a las imagenes seleccionadas
    for ($i=0; $i < $count; $i++) {
        $file = $_FILES['imagen'];

        process($file, $i);
        
        // //ruta donde se guadará el original
        // $target_path = "./images/1_original/";
        // //se añade el nombre a la ruta
        // $target_path = $target_path . $_FILES['imagen']['name'][$i];
        // echo "<pre>";
        // var_dump("->", $file['tmp_name'][$i]);
        // save_image($file['tmp_name'][$i], $target_path, $file['type'][$i]);
    }
    
    /*
     * Función principal donde se clasifican las imagenes según sus dimensiones.
     * @param  [type]     $file  [El array del archivo FILE image desde el input]
     * @param  [type]     $count [Es un contador para poder especificar la clave en el array a qué archivo manipular.]
     * @return [type]            [description]
     * @author Fabián Muñoz Flores
     * @date   2018-02-14
     */
    function process($file, $count)
    {
        //nombre del archivo seleccionado
        $name_file = basename($file['name'][$count]);
    
        //Archivo temporal desde el $_FILE
        $origen = $file['tmp_name'][$count];
        
        // ===== REDIMENSIONAR ===== //
        
        //Obtener las dimensiones
        $datos = getimagesize($origen);
        
        $width = (int) $datos['0']; //Ancho
        $height = (int) $datos['1']; //Alto
        
        //NOMBRE
        //convertir en array el nombre para obtener la ext.
        $d = explode('.', $name_file);
        
        //_sm
        $name_sm = $d[0].'_sm.'.$d[1];
        //_md
        $name_md = $d[0].'_md.'.$d[1];
        //_lg
        $name_lg = $d[0].'_lg.'.$d[1];
        
        //validar las dimensiones
        if ($width <= _x1) { //Si es menor a 400px de ancho
        
            //Si cae aquí, directamente se guardará en la carpeta de "small"
            //Se guardará la original con un nuevo nombre
            save_image($origen, "./images/small/".basename($name_sm), $file['type'][$count]);
        } elseif ($width > _x1 && $width <= _x2) { //Si es mayor a 400px y menor o igual a 800px de ancho
        
            //Si cae aquí, se guardará en la carpeta "medium",
            //pero también se creará una copia de la imagen con un tamaño de 400px de ancho
            //400
            redimensionar_jpeg($origen, "./images/small/".$name_sm, _x1, 100, $d[1]);
            //800
            //Se guardará la original con un nuevo nombre
            save_image($origen, "./images/medium/".basename($name_md), $file['type'][$count]);
        } elseif ($width > _x2 && $width <= _x3) { //Si es mayor a 800px y menor o igual a 1400px de ancho
        
            //Si cae aquí, se guardará en la carpeta "large",
            //pero también se creará una copia de la imagen con un tamaño de 800px de ancho y otra de 400px.
            //Cada una en sus respectivas carpetas
        
            //400
            redimensionar_jpeg($origen, "./images/small/".$name_sm, _x1, 100, $d[1]);
            //800
            redimensionar_jpeg($origen, "./images/medium/".$name_md, _x2, 100, $d[1]);
            //1400
            //Se guardará la original con un nuevo nombre
            save_image($origen, "./images/large/".basename($name_lg), $file['type'][$count]);
        } else {//Los que son mayores a 1400px de ancho
        
            //400
            redimensionar_jpeg($origen, "./images/small/".$name_sm, _x1, 100, $d[1]);
            //800
            redimensionar_jpeg($origen, "./images/medium/".$name_md, _x2, 100, $d[1]);
            //1400
            redimensionar_jpeg($origen, "./images/large/".$name_lg, _x3, 100, $d[1]);
        }
    }

    /* Función para subir una imagen al servidor
     * [save_image description]
     * @param  [type]     $file [Es de donde viene la imagen. Puede venir desde una ruta del servidor o desde el $_FILE del input]
     * @param  [type]     $path [Ruta y nombre de donde se generará la nueva imagen]
     * @param  [type]     $ext  [Extensión de la imagen a guardar]
     * @return [type]           [description]
     * @author Fabián Muñoz Flores
     * @date   2018-02-14
     */
    function save_image($file, $path, $ext)
    {
        $extensiones = array(0=>'image/jpg',1=>'image/jpeg',2=>'image/png');
        
        if (in_array($ext, $extensiones)) {
            if (move_uploaded_file($file, $path)) {
                echo "[".$path."] : Archivo guardado con existo.... Ok. <br>";
            } else {
                echo "error<br>";
                echo "<pre>";
                var_dump("->", $file, $path);
            }
        } else {
            echo "error ext.<br>";
        }
    }
    
    /*
     * Función para redimensionar imagenes
     * @param  [type]     $img_original      [Es de donde viene la imagen. Puede venir desde una ruta del servidor o desde el $_FILE del input]
     * @param  [type]     $img_nueva         [Ruta y nombre de donde se generará la nueva imagen]
     * @param  [type]     $img_nueva_anchura [Ancho de la nueva imagen. A destacar que la altura es proporcional al ancho. Para que no se deforme.]
     * @param  [type]     $calidad           [Calidad de la imagen. En jpeg es de 1 a 100, donde 100 es la mejor. Pero en PNG es de 0-9 donde 9 es la más comprimida]
     * @param  [type]     $ext               [La extensión de la imagen a generar. La extenión quedará igual a la original]
     * @return [type]                        [description]
     * @author Fabián Muñoz Flores
     * @date   2018-02-14
     */
    function redimensionar_jpeg($img_original, $img_nueva, $img_nueva_anchura, $calidad, $ext)
    {

        // crear imagen desde original
        if ($ext === "png") { //si es png
            $img = imagecreatefrompng($img_original);
        } else {
            $img = ImageCreateFromJPEG($img_original);
        }
        $width = imagesx($img);//ancho
        $height = imagesy($img);//alto
        
        //El alto será proporcional al ancho.
        $nueva_altura = ($img_nueva_anchura * $height) / $width ; // tamaño proporcional
        
        // crear imagen nueva
        $thumb = imagecreatetruecolor($img_nueva_anchura, $nueva_altura);

        // redimensionar imagen original copiandola en la imagen
        ImageCopyResized($thumb, $img, 0, 0, 0, 0, $img_nueva_anchura, $nueva_altura, $width, $height);

        //convertir en array el nombre para obtener la ext.
        $d = explode('.', $name_file);

        // guardar la imagen redimensionada donde indicia $img_nueva
        if ($ext === "png") { //si es png
            Imagepng($thumb, $img_nueva, 0);
        } else {
            ImageJPEG($thumb, $img_nueva, $calidad);
        }
        
        echo "[".$img_nueva."] : Archivo redimensionado.... Ok. <br>";
    }

    echo '<a href="./index.html">Regresar</a>';
