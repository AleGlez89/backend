<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appProdUrlMatcher.
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        // nodos
        if (rtrim($pathinfo, '/') === '') {
            if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                $allow = array_merge($allow, array('GET', 'HEAD'));
                goto not_nodos;
            }

            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'nodos');
            }

            return array (  '_controller' => 'TrabajoBundle\\Controller\\DefaultController::indexAction',  '_route' => 'nodos',);
        }
        not_nodos:

        if (0 === strpos($pathinfo, '/nodo')) {
            // trabajo_default_add
            if ($pathinfo === '/nodo/adicionar') {
                return array (  '_controller' => 'TrabajoBundle\\Controller\\DefaultController::addAction',  '_route' => 'trabajo_default_add',);
            }

            // trabajo_default_move
            if ($pathinfo === '/nodo/mover') {
                return array (  '_controller' => 'TrabajoBundle\\Controller\\DefaultController::moveAction',  '_route' => 'trabajo_default_move',);
            }

            // trabajo_default_del
            if ($pathinfo === '/nodo/eliminar') {
                return array (  '_controller' => 'TrabajoBundle\\Controller\\DefaultController::delAction',  '_route' => 'trabajo_default_del',);
            }

        }

        // homepage
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'homepage');
            }

            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::indexAction',  '_route' => 'homepage',);
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
