<?php

namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class Framework
{
    protected $matcher;
    protected $resolver;

    public function __construct(UrlMatcher $matcher, ControllerResolver $resolver)
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
    }

    public function handle(Request $request)
    {
      
        $this->matcher->getContext()->fromRequest($request);

        try {
          
          $request->attributes->add($this->matcher->match($request->getPathInfo()));

          $controller = $this->resolver->getController($request);
          $arguments = $this->resolver->getArguments($request, $controller);


          return call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $exception) {
            throw $exception;
            return new Response('Not Found', 404);
        } catch (\Exception $exception) {
            throw $exception;
            return new Response('An error occurred', 500);
        }
    }
}