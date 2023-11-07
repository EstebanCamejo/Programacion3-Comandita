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
}
?>