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
require_once './controllers/EncuestaController.php';
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
$app->group('/usuarios', function (RouteCollectorProxy $group){
  
  $group->get('[/]', \UsuarioController::class . ':TraerTodos'); ////***** */
  $group->get('/{dni}', \UsuarioController::class . ':TraerUno'); ////***** */
  $group->post('[/]', \UsuarioController::class . ':CargarUno'); ////***** */
  $group->delete('/borrar/{id}', \UsuarioController::class . ':BorrarUno'); ////***** */
  $group->post('/baja[/]', \UsuarioController::class . ':BajarUno'); ////***** */
  $group->post('/modificacion[/]', \UsuarioController::class . ':ModificarUno');  ////***** */

  })->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
  ->add(\AutentificadorMiddleware::class . ':verificarToken');


// peticiones Productos
$app->group('/productos', function (RouteCollectorProxy $group){

  $group->post('/importar-csv[/]', \ProductoController::class . ':ImportarTabla');  ///***** */
  $group->get('/guardar[/]', \ProductoController::class . ':ExportarTabla');  ///***** */

  $group->get('[/]', \ProductoController::class . ':TraerTodos');////***** */
  $group->get('/{id}', \ProductoController::class . ':TraerUno'); ////***** */
  $group->post('[/]', \ProductoController::class . ':CargarUno');////**** */
  $group->delete('/baja/{id}', \ProductoController::class . ':BorrarUno');////**** */
  $group->post('/modificacion[/]', \ProductoController::class . ':ModificarUno');////***** */
  
})->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
->add(\AutentificadorMiddleware::class . ':verificarToken');

 

// peticiones Mesa
$app->group('/mesasSocios', function (RouteCollectorProxy $group){

  $group->get('[/]', \MesaController::class . ':TraerTodos');////***** */
  $group->delete('/baja/{codigoMesa}', \MesaController::class . ':BorrarUno');////***** */
  $group->post('/cerrar[/]', \MesaController::class . ':CerrarUno');  ////***** */
  $group->get('/masUsada[/]', \MesaController::class . ':MasUsada'); ////***** */

})->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
->add(\AutentificadorMiddleware::class . ':verificarToken');


// peticiones Pedidos
$app->group('/pedidosSocio', function (RouteCollectorProxy $group){  

  $group->get('[/]', \PedidoController::class . ':TraerTodos');////***** */
  $group->get('/demora/{codigoPedido}', \PedidoController::class . ':TraerDemoraDeUno');////***** */
  $group->get('/encuesta[/]', \EncuestaController::class . ':TraerMejoresComentarios');////***** */

})->add(\AutentificadorMiddleware::class . ':verificarRolSocio')
->add(\AutentificadorMiddleware::class . ':verificarToken');

 // 12- Alguno de los socios pide los mejores comentarios

///--------------------------------------MOZO------------------------------------------------

// peticiones Mesa
$app->group('/mesasMozo', function (RouteCollectorProxy $group){

  $group->get('[/]', \MesaController::class . ':TraerTodos');////***** */
  $group->get('/{codigoMesa}', \MesaController::class . ':TraerUno'); ////***** */
  $group->post('[/]', \MesaController::class . ':CargarUno');////***** */
  $group->post('/modificacion[/]', \MesaController::class . ':ModificarUno');////***** */

})->add(\AutentificadorMiddleware::class . ':verificarRolMozo')
->add(\AutentificadorMiddleware::class . ':verificarToken');;

// peticiones Pedidos
$app->group('/pedidos', function (RouteCollectorProxy $group){
  
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno');////***** */
  $group->post('[/]', \PedidoController::class . ':CargarUno');////***** */
  $group->delete('/baja/{codigoPedido}', \PedidoController::class . ':BorrarUno');////***** */
  $group->post('/cancelar[/]', \PedidoController::class . ':CancelarUno'); ////***** */
  $group->post('/subirfoto[/]', \PedidoController::class . ':SubirFoto');////***** */
  $group->post('/modificacion[/]', \PedidoController::class . ':ModificarUno');///***** */
  $group->get('[/]', \PedidoController::class . ':ListosParaServir');///***** */
  $group->post('/cobrar[/]', \PedidoController::class . ':CobrarCuenta');///***** */
  /// ******** 9- La moza cobra la cuenta.************COBRAR MESA

})->add(\AutentificadorMiddleware::class . ':verificarRolMozo')
->add(\AutentificadorMiddleware::class . ':verificarToken');

$app->group('/pedidoproducto', function (RouteCollectorProxy $group){

  $group->get('[/]', \PedidoProductoController::class . ':TraerTodos');///***** */
  $group->get('/{id}', \PedidoProductoController::class . ':TraerUno');///***** */
  $group->post('[/]', \PedidoProductoController::class . ':CargarUno');///***** */
  $group->delete('/baja/{codigoPedido}', \PedidoProductoController::class . ':BorrarUno');// funciona OK 

})->add(\AutentificadorMiddleware::class . ':verificarRolMozo')
->add(\AutentificadorMiddleware::class . ':verificarToken');



///--------------------------------------OTROS-USUARIOS------------------------------------------------




//2 Bartender 3 Cervecero 4 Cocinero 6 Pastelero
// peticiones PedidoProducto 
$app->group('/pedidoproducto', function (RouteCollectorProxy $group){

  $group->get('/pendiente/{sector}', \PedidoProductoController::class . ':TraerPendientes');  
  $group->post('/modificacion[/]', \PedidoProductoController::class . ':ModificarUno');// funciona OK

})->add(\AutentificadorMiddleware::class . ':verificarRolOtrosUsuarios')
->add(\AutentificadorMiddleware::class . ':verificarToken');



///-----------------------------------------CLIENTE-------------------------------------------------


//$app->post('/cliente[/]',  \ClienteController::class . ':TraerPedido');
//$app->post('/encuesta[/]', \ClienteController::class . ':CargarUno');


$app->group('/cliente', function (RouteCollectorProxy $group) {

  $group->post('[/]',  \ClienteController::class . ':TraerPedido');
  $group->post('/encuesta[/]',  \EncuestaController::class . ':Encuesta');

});


// Run app
$app->run();

?>