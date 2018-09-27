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
// Date:     27/09/2018
// Project:  Router
//
declare(strict_types=1);
namespace CodeInc\Router\Controllers;
use CodeInc\Router\Controllers\ControllerInstantiatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


/**
 * Class ControllerRequestHandlerWrapper
 *
 * @package CodeInc\Router
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class ControllerRequestHandlerWrapper implements RequestHandlerInterface
{
    /**
     * @var ControllerInstantiatorInterface
     */
    private $controllerInstantiator;

    /**
     * @var string
     */
    private $controllerClass;

    /**
     * ControllerRequestHandlerWrapper constructor.
     *
     * @param string $controllerClass
     * @param ControllerInstantiatorInterface $controllerInstantiator
     */
    public function __construct(string $controllerClass, ControllerInstantiatorInterface $controllerInstantiator)
    {
        $this->controllerClass = $controllerClass;
        $this->controllerInstantiator = $controllerInstantiator;
    }

    /**
     * @inheritdoc
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request):ResponseInterface
    {
        return $this->controllerInstantiator->instantiate($this->controllerClass, $request)->getResponse();
    }
}