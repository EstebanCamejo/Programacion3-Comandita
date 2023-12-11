<?php

    require_once './db/AccesoDatos.php';

    class manejadorCSV
    {   
        public static function grabarEnCsv($item, $ruta)
        {
            if ($item) {
                $separadoPorComa = implode(",", (array) $item);
                $file = fopen($ruta, "a+");
                if ($file) {
                    fwrite($file, $separadoPorComa . PHP_EOL);
                    fclose($file);
                } else {
                    throw new Exception("No se pudo abrir el archivo CSV para escritura.");
                }
            } else {
                throw new Exception("Los datos para exportar son inválidos.");
            }
        }


        public static function exportarTabla($tabla, $clase, $ruta)
        {
            $listaProductos = AccesoDatos::obtenerTodos($tabla, $clase);
            if ($listaProductos) {
                foreach ($listaProductos as $item) {
                    self::grabarEnCsv($item, $ruta);
                }
            } else {
                throw new Exception("No se encontraron datos para exportar.");
            }
        }

        
        public static function LeerCsv($archivo)
        {
            $auxArchivo = fopen($archivo, "r");
            $array = [];

            if ($auxArchivo) {
                try {
                    while (!feof($auxArchivo)) {
                        $registro = fgets($auxArchivo);
                        if (!empty($registro)) {
                            $registro = str_replace('"', '', $registro); // Eliminar comillas dobles
                            array_push($array, $registro);
                        }
                    }
                } catch (\Throwable $e) {
                    echo "No se pudo leer el archivo<br>";
                    printf($e);
                } finally {
                    fclose($auxArchivo);
                    return $array;
                }
            }
        }
        
    }







?>