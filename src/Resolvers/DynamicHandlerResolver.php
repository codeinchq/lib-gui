<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2018 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material is strictly forbidden unless prior    |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     24/09/2018
// Project:  Router
//
declare(strict_types=1);
namespace CodeInc\Router\Resolvers;
use CodeInc\Router\Exceptions\NotARequestHandlerException;
use CodeInc\Router\Exceptions\NotWithinNamespaceException;
use CodeInc\Router\Exceptions\RouterEmptyHandlersNamespaceException;
use CodeInc\Router\Exceptions\RouterEmptyUriPrefixException;
use Psr\Http\Server\RequestHandlerInterface;


/**
 * Class DynamicHandlerResolver
 *
 * @package CodeInc\Router\Resolvers
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class DynamicHandlerResolver implements HandlerResolverInterface
{
    /**
     * @var string
     */
    protected $handlersNamespace;

    /**
     * @var string
     */
    protected $uriPrefix;

    /**
     * DynamicResolver constructor.
     *
     * @param string $handlersNamespace
     * @param string $uriPrefix
     */
    public function __construct(string $handlersNamespace, string $uriPrefix)
    {
        if (empty($uriPrefix)) {
            throw new RouterEmptyUriPrefixException($this);
        }
        if (empty($handlersNamespace)) {
            throw new RouterEmptyHandlersNamespaceException($this);
        }
        $this->handlersNamespace = $handlersNamespace;
        $this->uriPrefix = $uriPrefix;
    }

    /**
     * @return string
     */
    public function getHandlersNamespace():string
    {
        return $this->handlersNamespace;
    }

    /**
     * @return string
     */
    public function getUriPrefix():string
    {
        return $this->uriPrefix;
    }

    /**
     * @inheritdoc
     * @param string $route
     * @return null|string
     */
    public function getHandlerClass(string $route):?string
    {
        if (substr($route, 0, strlen($this->uriPrefix)) == $this->uriPrefix) {
            $controllerClass = $this->handlersNamespace.'\\'
                .str_replace('/', '\\', substr($route, strlen($this->uriPrefix)));
            if (class_exists($controllerClass)
                && is_subclass_of($controllerClass, RequestHandlerInterface::class)) {
                return $controllerClass;
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     * @param string $handlerClass
     * @return string
     */
    public function getHandlerRoute(string $handlerClass):string
    {
        if (!is_subclass_of($handlerClass, RequestHandlerInterface::class)) {
            throw new NotARequestHandlerException($handlerClass);
        }
        if (!substr($handlerClass, 0, strlen($this->handlersNamespace)) == $handlerClass) {
            throw new NotWithinNamespaceException($handlerClass, $this->handlersNamespace);
        }
        return $this->uriPrefix
            .str_replace('\\', '/',
                substr($handlerClass, strlen($this->handlersNamespace) + 1));
    }
}