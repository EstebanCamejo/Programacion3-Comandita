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
require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/AutentificadorMiddleware.php';


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




///--------------------------------------LOGIN------------------------------------------------

//Genera el Token
$app->post('/login[/]',  \UsuarioController::class . ':Login');



///--------------------------------------SOCIOS------------------------------------------------

// peticiones ABM Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');// funciona OK    
  $group->get('/{dni}', \UsuarioController::class . ':TraerUno');// funciona OK
  $group->post('[/]', \UsuarioController::class . ':CargarUno');// funciona OK  
  $group->delete('/borrar/{id}', \UsuarioController::class . ':BorrarUno'); // funciona OK
  $group->post('/baja[/]', \UsuarioController::class . ':BajarUno'); // funciona OK  
  $group->post('/modificacion[/]', \UsuarioController::class . ':ModificarUno');  

  })->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
  ->add(\AutentificadorMiddleware::class . ':verificarToken');


// peticiones Productos
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');// funciona OK
  $group->get('/{id}', \ProductoController::class . ':TraerUno'); // funciona OK
  $group->post('[/]', \ProductoController::class . ':CargarUno');// funciona OK
  $group->delete('/baja/{id}', \ProductoController::class . ':BorrarUno');// funciona OK
  $group->post('/modificacion[/]', \ProductoController::class . ':ModificarUno');// funciona OK
})->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
->add(\AutentificadorMiddleware::class . ':verificarToken');

 

// peticiones Mesa
$app->group('/mesasSocios', function (RouteCollectorProxy $group) {

  $group->get('[/]', \MesaController::class . ':TraerTodos');// funciona OK
  $group->delete('/baja/{codigoMesa}', \MesaController::class . ':BorrarUno');// funciona OK 
  $group->post('/cerrar[/]', \MesaController::class . ':CerrarUno'); // funciona OK 
 
  //  13- Alguno de los socios pide la mesa más usada

})->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
->add(\AutentificadorMiddleware::class . ':verificarToken');;


// peticiones Pedidos
$app->group('/pedidosSocio', function (RouteCollectorProxy $group) {  

  $group->get('[/]', \PedidoController::class . ':TraerTodos');// funciona OK
  $group->get('/demora/{codigoPedido}', \PedidoController::class . ':TraerDemoraDeUno');

})->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
->add(\AutentificadorMiddleware::class . ':verificarToken');

 // 12- Alguno de los socios pide los mejores comentarios





///--------------------------------------MOZO------------------------------------------------



// peticiones Mesa
$app->group('/mesasMozo', function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');// funciona OK
  $group->get('/{codigoMesa}', \MesaController::class . ':TraerUno'); // funciona OK
  $group->post('[/]', \MesaController::class . ':CargarUno');// funciona OK
  $group->post('/modificacion[/]', \MesaController::class . ':ModificarUno');// funciona OK

/// ******** 9- La moza cobra la cuenta.************COBRAR MESA

})->add(\AutentificadorMiddleware::class . ':verificarRolMozo')
->add(\AutentificadorMiddleware::class . ':verificarToken');;

// peticiones Pedidos
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno');// funciona OK
  $group->post('[/]', \PedidoController::class . ':CargarUno');// funciona OK
  $group->delete('/baja/{codigoPedido}', \PedidoController::class . ':BorrarUno'); // funciona OK
  $group->post('/cancelar[/]', \PedidoController::class . ':CancelarUno'); // funciona OK
  $group->post('/subirfoto[/]', \PedidoController::class . ':SubirFoto');
  $group->get('[/]', \PedidoController::class . ':ListosParaServir');
  $group->post('/modificacion[/]', \PedidoController::class . ':ModificarUno');// funciona OK
/////cambia el estado de la mesa,************

})/*->add(\AutentificadorMiddleware::class . ':verificarRolMozo')
->add(\AutentificadorMiddleware::class . ':verificarToken')*/;

$app->group('/pedidoproducto', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoProductoController::class . ':TraerTodos');// funciona OK
  $group->get('/{id}', \PedidoProductoController::class . ':TraerUno');// funciona OK
  $group->post('[/]', \PedidoProductoController::class . ':CargarUno');// funciona OK
  $group->delete('/baja/{codigoPedido}', \PedidoProductoController::class . ':BorrarUno');// funciona OK 

})->add(\AutentificadorMiddleware::class . ':verificarRolMozo')
->add(\AutentificadorMiddleware::class . ':verificarToken');



///--------------------------------------OTROS-USUARIOS------------------------------------------------




//2 Bartender 3 Cervecero 4 Cocinero 6 Pastelero
// peticiones PedidoProducto 
$app->group('/pedidoproducto', function (RouteCollectorProxy $group) {
  $group->get('/pendiente/{sector}', \PedidoProductoController::class . ':TraerPendientes');  
  $group->post('/modificacion[/]', \PedidoProductoController::class . ':ModificarUno');// funciona OK
})->add(\AutentificadorMiddleware::class . ':verificarRolOtrosUsuarios')
->add(\AutentificadorMiddleware::class . ':verificarToken');



///-----------------------------------------CLIENTE-------------------------------------------------

/// ********  11- El cliente ingresa el código de mesa y el del pedido junto con los datos de la encuesta
$app->post('/cliente[/]',  \ClienteController::class . ':TraerPedido');
//$app->post('/cliente[/]', \ClienteController::class . ':CargarUno');




// Run app
$app->run();

?>