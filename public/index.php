<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/PedidoProductoController.php';
require_once './controllers/ClienteController.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->setBasePath('/public');
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("hola alumnos de los lunes!");
    return $response;
});

// peticiones Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos'); // funciona OK
    $group->get('/{dni}', \UsuarioController::class . ':TraerUno');// funciona OK
    $group->post('[/]', \UsuarioController::class . ':CargarUno');// funciona OK
    
    $group->delete('/borrar/{id}', \UsuarioController::class . ':BorrarUno'); // funciona OK
    $group->post('/baja[/]', \UsuarioController::class . ':BajarUno'); // funciona OK

    $group->post('/modificacion[/]', \UsuarioController::class . ':ModificarUno');
  });

// peticiones Productos
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');// funciona OK
  $group->get('/{id}', \ProductoController::class . ':TraerUno'); // funciona OK
  $group->post('[/]', \ProductoController::class . ':CargarUno');// funciona OK

  $group->delete('/baja/{id}', \ProductoController::class . ':BorrarUno');// funciona OK
  
  $group->post('/modificacion[/]', \ProductoController::class . ':ModificarUno');// funciona OK
});

// peticiones Mesa
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');// funciona OK
  $group->get('/{codigoMesa}', \MesaController::class . ':TraerUno'); // funciona OK
  $group->post('[/]', \MesaController::class . ':CargarUno');// funciona OK

  $group->delete('/baja/{codigoMesa}', \MesaController::class . ':BorrarUno');// funciona OK 
  $group->post('/cerrar[/]', \MesaController::class . ':CerrarUno'); // funciona OK 

  $group->post('/modificacion[/]', \MesaController::class . ':ModificarUno');// funciona OK

});


// peticiones Pedidos
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');// funciona OK
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno');// funciona OK
  $group->post('[/]', \PedidoController::class . ':CargarUno');// funciona OK

  $group->delete('/baja/{codigoPedido}', \PedidoController::class . ':BorrarUno'); // funciona OK
  $group->post('/cancelar[/]', \PedidoController::class . ':CancelarUno'); // funciona OK
  
  $group->post('/modificacion[/]', \PedidoController::class . ':ModificarUno');// funciona OK

  $group->get('/demora/{codigoPedido}', \PedidoController::class . ':TraerDemoraDeUno');
});


// peticiones PedidoProducto
$app->group('/pedidoproducto', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoProductoController::class . ':TraerTodos');// funciona OK
  $group->get('/{id}', \PedidoProductoController::class . ':TraerUno');// funciona OK
  $group->post('[/]', \PedidoProductoController::class . ':CargarUno');// funciona OK
  $group->get('/pendiente/{sector}', \PedidoProductoController::class . ':TraerPendientes');
  $group->delete('/baja/{codigoPedido}', \PedidoProductoController::class . ':BorrarUno');// funciona OK 

  
  $group->post('/modificacion[/]', \PedidoProductoController::class . ':ModificarUno');// funciona OK
});


$app->post('/cliente',  \ClienteController::class . ':TraerPedido');
//$app->post('/cliente[/]', \ClienteController::class . ':CargarUno');
$app->post('/login',  \UsuarioController::class . ':Login');



// Run app
$app->run();

?>