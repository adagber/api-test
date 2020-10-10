<?php
require_once __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
 
$dotenv = Dotenv::create(__DIR__.'/..');
$dotenv->load();
$dotenv->required('APP_ENV');

$env = getenv('APP_ENV');

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../src/app.php';

$dumper = new PhpMatcherDumper($routes);
//$matcher = $dumper->dump();

$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);
$resolver = new ControllerResolver();

$framework = new Simplex\Framework($matcher, $resolver);
$response = $framework->handle($request);

$response->send();