<?php
require_once './models/Producto.php';
require_once './ArchivosCSV/manejadorCSV.php';

class ProductoController 
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $sector = $parametros['sector'];
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $tiempoPreparacion = $parametros['tiempoPreparacion'];       

        // Creamos el producto
        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->sector = $sector;
        $producto->precio = $precio;
        $producto->tiempoPreparacion = $tiempoPreparacion;

        $producto->fechaAlta = date ('Y-m-d H:i:s');
        $producto->fechaModificacion = date (':iY-m-d H:s');//"0000-00-00";
        $producto->fechaBaja = date (':iY-m-d H:s');//"0000-00-00";
        $producto->activo = 1;

        $producto->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito",
        "nombre"=>$producto->nombre,
        "sector"=>$producto->sector,
        "precio"=>$producto->precio,
        "tiempoPreparacion"=>$producto->tiempoPreparacion,
        "fechaAlta"=>$producto->fechaAlta),JSON_PRETTY_PRINT);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
       
        if(!empty($lista))
        {
            $payload = json_encode(array("lista de productos" => $lista),JSON_PRETTY_PRINT);
        }else
        {
            $payload = json_encode(array("mensaje" => "Error, no se encontro una lista de productos"),JSON_PRETTY_PRINT);
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por id
        $id = $args['id'];
        $producto = Producto::obtenerProducto($id);

        if(!empty($producto))
        {
            $payload = json_encode(array("Producto encontrado"=>$producto),JSON_PRETTY_PRINT);
        }else
        {
            $payload = json_encode(array("Error, producto no encontrado"),JSON_PRETTY_PRINT);
        }       
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function BorrarUno($request, $response, $args)
    {
        $productoId = $args["id"];
        $productoBorrado = Producto::borrarProducto($productoId);
        if($productoBorrado)
        {
            $payload = json_encode(array("mensaje" => "Producto borrado con exito"),JSON_PRETTY_PRINT);
        }else
        {
            $payload = json_encode(array("mensaje" => "Error, el producto no fue borrado"),JSON_PRETTY_PRINT);
        }
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $sector = $parametros['sector'];
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $tiempoPreparacion = $parametros['tiempoPreparacion'];        

        // Creamos el Producto
        $producto = new Producto();
        $producto->id = $id;
        $producto->sector = $sector;
        $producto->nombre = $nombre;
        $producto->precio = $precio;
        $producto->tiempoPreparacion = $tiempoPreparacion;      

        $productoModificado = Producto::modificarProducto($producto);
        if($productoModificado)
        {
            $payload = json_encode(array("mensaje" => "Producto modificado con exito",
            "sector"=>$producto->sector,
            "nombre"=>$producto->nombre,
            "precio"=>$producto->precio,
            "tiempoPreparacion"=>$producto->tiempoPreparacion),JSON_PRETTY_PRINT);        
        }else
        {
            $payload = json_encode(array("mensaje" => "Error, el producto no se modifico"),JSON_PRETTY_PRINT);
        }   

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function exportarTabla($request, $response, $args)
    {
        try {
            manejadorCSV::exportarTabla('producto', 'Producto', 'producto.csv');
            $payload = json_encode("Tabla exportada con éxito");
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
            return $newResponse;
        } catch (\Throwable $mensaje) {
            // Aquí maneja los errores si ocurrieron durante la exportación
            $response->getBody()->write("Error al exportar: " . $mensaje->getMessage());
            return $response->withStatus(500); // Devuelve un estado de error 500
        }
    }


    public function ImportarTabla($request, $response, $args)
    {
        try
        {
            $archivo = ($_FILES["archivo"]);          
            Producto::CargarCSV($archivo["tmp_name"]);
            $payload = json_encode("Carga exitosa.");
            $response->getBody()->write($payload);
            $newResponse = $response->withHeader('Content-Type', 'application/json');
        }
        catch(Throwable $mensaje)
        {
            printf("Error al listar: <br> $mensaje .<br>");
        }
        finally
        {
            return $newResponse;
        }    
    }

    public function ListarProductoOdenadoPorMayorVenta($request, $response, $args)
    {
        $lista = Producto::obtenerProductosMasVendidos();
        if(!empty($lista))
        {
          $payload = json_encode(array("lista de Productos mas vendidos" => $lista),JSON_PRETTY_PRINT);
        }else
        {
          $payload = json_encode(array("Error. No se encontraron productos"),JSON_PRETTY_PRINT);
        }        

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}
?>