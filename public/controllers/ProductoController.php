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

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por id
        $id = $args['id'];
        $producto = Producto::obtenerProducto($id);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function BorrarUno($request, $response, $args)
    {
        $productoId = $args["id"];
        Producto::borrarProducto($productoId);

        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

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
        $activo = $parametros['activo'];

        // Creamos el Producto
        $producto = new Producto();
        $producto->id = $id;
        $producto->sector = $sector;
        $producto->nombre = $nombre;
        $producto->precio = $precio;
        $producto->tiempoPreparacion = $tiempoPreparacion;
        $producto->activo = $activo;

        Producto::modificarProducto($producto);

        $payload = json_encode(array("mensaje" => "Producto modificado con exito"));

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

/*
    public function ExportarTabla($request, $response, $args)
    {
        try
        {
            manejadorCSV::exportarTabla('producto', 'Producto', 'producto.csv');
            $payload = json_encode("Tabla exportada con éxito");
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
    }*/

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

}
?>