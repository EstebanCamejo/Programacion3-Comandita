<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Dompdf\Dompdf;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/PedidoProductoController.php';
require_once './controllers/ClienteController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/LogController.php';

require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/AutentificadorMiddleware.php';
require_once './middlewares/LogMiddleware.php';




date_default_timezone_set('America/Buenos_Aires'); 

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


///--------------------------------------ADMIN------------------------------------------------

// peticiones ABM Usuarios
$app->group('/usuarios', function (RouteCollectorProxy $group){
  
  $group->get('[/]', \UsuarioController::class . ':TraerTodos'); ////*****OK */
  $group->get('/{dni}', \UsuarioController::class . ':TraerUno'); ////*****OK */
  $group->post('[/]', \UsuarioController::class . ':CargarUno'); ////*****OK */
  $group->delete('/borrar/{id}', \UsuarioController::class . ':BorrarUno'); ////*****ERROR */
  $group->post('/baja[/]', \UsuarioController::class . ':BajarUno'); ////*****OK */
  $group->post('/modificacion[/]', \UsuarioController::class . ':ModificarUno');  ////*****OK */

  })
  ->add(\AutentificadorMiddleware::class . ':verificarToken')
  ->add(\AutentificadorMiddleware::class . ':verificarRolSocio');


// peticiones Productos
$app->group('/productos', function (RouteCollectorProxy $group){

  $group->get('[/]', \ProductoController::class . ':TraerTodos');////*****OK */
  $group->get('/{id}', \ProductoController::class . ':TraerUno'); ////*****OK */
  $group->post('[/]', \ProductoController::class . ':CargarUno');////****OK */
  $group->delete('/baja/{id}', \ProductoController::class . ':BorrarUno');////****OK */
  $group->post('/modificacion[/]', \ProductoController::class . ':ModificarUno');////*****ERROR */
   
})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolSocio');

 
  //ARCHIVOS CSV
$app->group('/archivoProductos', function (RouteCollectorProxy $group){

  $group->post('/importar-csv[/]', \ProductoController::class . ':ImportarTabla');  ///*****OK */
  $group->get('/guardar[/]', \ProductoController::class . ':ExportarTabla');  ///*****OK */
})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolSocio');

// ARCHIVO PDF
$app->group('/archivoPdf', function (RouteCollectorProxy $group){

  $group->get('/guardarLogoPdf[/]', \UsuarioController::class . ':ExportarPdf');
})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolSocio');

// peticiones Mesas

$app->group('/mesasSocios', function (RouteCollectorProxy $group){

  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->delete('/baja/{codigoMesa}', \MesaController::class . ':BorrarUno');
  $group->post('/cerrar[/]', \MesaController::class . ':CerrarUno');
  $group->get('/masUsada[/]', \MesaController::class . ':MasUsada');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->get('/facturas[/]', \MesaController::class . ':MasBarataAMasCara');
  $group->post('/facturacionEntreFechas[/]', \MesaController::class . ':facturacionEntreDosFechas');    //NO

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolSocio');


// peticiones Pedidos
$app->group('/pedidosSocio', function (RouteCollectorProxy $group){  

  $group->get('[/]', \PedidoController::class . ':TraerTodos');////*****OK */
  $group->get('/demora/{codigoPedido}', \PedidoController::class . ':TraerDemoraDeUno');////*****OK */
  $group->get('/encuesta[/]', \EncuestaController::class . ':TraerMejoresComentarios');////*****OK */
  $group->get('/pedidosVencidos[/]', \PedidoController::class . ':TraerPedidosVencidos');////*****OK *

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolSocio');



$app->group('/socioLogs', function (RouteCollectorProxy $group){  

  $group->get('[/]', \LogController::class . ':CantidadDeOperacionesPorSector');////*****OK */
  $group->get('/empleadoYSector[/]', \LogController::class . ':CantidadDeOperacionesPorEmpleadoYSector');////*****OK */
  $group->post('/empleadoDiasHorarios[/]', \LogController::class . ':EmpleadoDiasYHorarios');////*****OK */


})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolSocio');

// peticiones PedidoProducto
$app->group('/socioPedidoProducto', function (RouteCollectorProxy $group){  

  $group->get('/ordenadosPorVenta[/]', \ProductoController::class . ':ListarProductoOdenadoPorMayorVenta');
 
  
})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolSocio');
///--------------------------------------MOZO------------------------------------------------

// peticiones Mesa
$app->group('/mesasMozo', function (RouteCollectorProxy $group){

  $group->get('[/]', \MesaController::class . ':TraerTodos');////*****OK */
  $group->get('/{codigoMesa}', \MesaController::class . ':TraerUno'); ////*****OK */ 
  $group->post('/modificacion[/]', \MesaController::class . ':ModificarUno');////*****OK */

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolMozo');

// peticiones Pedidos
$app->group('/pedidos', function (RouteCollectorProxy $group){
  
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno');////*****OK */
  $group->post('[/]', \PedidoController::class . ':CargarUno');////*****OK */
  $group->delete('/baja/{codigoPedido}', \PedidoController::class . ':BorrarUno');////*****OK */
  $group->post('/cancelar[/]', \PedidoController::class . ':CancelarUno'); ////*****OK */
  $group->post('/subirfoto[/]', \PedidoController::class . ':SubirFoto');////*****OK */
  $group->post('/modificacion[/]', \PedidoController::class . ':ModificarUno');///*****OK */
  $group->get('[/]', \PedidoController::class . ':ListosParaServir');///*****OK */
  $group->post('/cobrar[/]', \PedidoController::class . ':CobrarCuenta');///*****OK */

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolMozo');

//Pedido PRODUCTO
$app->group('/pedidoproducto', function (RouteCollectorProxy $group){

  $group->get('[/]', \PedidoProductoController::class . ':TraerTodos');///*****OK */
  $group->get('/{id}', \PedidoProductoController::class . ':TraerUno');///*****OK */
  $group->post('[/]', \PedidoProductoController::class . ':CargarUno');///*****OK */
  $group->delete('/baja/{id}', \PedidoProductoController::class . ':BorrarUno');///*****OK */

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolMozo');



///--------------------------------------OTROS-USUARIOS------------------------------------------------

//3 Cervecero 
$app->group('/pedidoproductoCervecero', function (RouteCollectorProxy $group){

  $group->get('/pendiente/{sector}', \PedidoProductoController::class . ':TraerPendientes');  ///*****OK */
  $group->post('/modificacion[/]', \PedidoProductoController::class . ':ModificarUno');;///*****OK */

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolCervecero');


//4 Cocinero 
$app->group('/pedidoproductoCocinero', function (RouteCollectorProxy $group){

  $group->get('/pendiente/{sector}', \PedidoProductoController::class . ':TraerPendientes');  ///*****OK */
  $group->post('/modificacion[/]', \PedidoProductoController::class . ':ModificarUno');///*****OK */

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolCocinero');

//5 Bartender 
$app->group('/pedidoproductoBartender', function (RouteCollectorProxy $group){

  $group->get('/pendiente/{sector}', \PedidoProductoController::class . ':TraerPendientes');  ///*****OK */
  $group->post('/modificacion[/]', \PedidoProductoController::class . ':ModificarUno');///*****OK */

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolBartender');

//6 Pastelero
$app->group('/pedidoproductoPastelero', function (RouteCollectorProxy $group){

  $group->get('/pendiente/{sector}', \PedidoProductoController::class . ':TraerPendientes');  ///*****OK */
  $group->post('/modificacion[/]', \PedidoProductoController::class . ':ModificarUno');///*****OK */

})
->add(\AutentificadorMiddleware::class . ':verificarToken')
->add(\AutentificadorMiddleware::class . ':verificarRolPastelero');


///-----------------------------------------CLIENTE-------------------------------------------------

//$app->post('/encuesta[/]', \ClienteController::class . ':CargarUno');

$app->group('/cliente', function (RouteCollectorProxy $group) {

  $group->post('[/]',  \ClienteController::class . ':TraerPedido');///*****OK */
  $group->post('/encuesta[/]',  \EncuestaController::class . ':Encuesta');///*****OK */

});

$app->add(\LogMiddleware::class);

$app->run();

?>