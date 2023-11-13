<?php
require_once './models/Producto.php';
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
}
?>