<?php
namespace App\Handlers;

//use Psr\Http\Message\ResponseInterface;
//use Psr\Http\Message\ServerRequestInterface;
use Monolog\Logger;
use Slim\Http\Body;
use UnexpectedValueException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PhpError extends \Slim\Handlers\PhpError
{
    protected $logger;

    public function __construct($displayErrorDetails, Logger $logger)
    {
        parent::__construct($displayErrorDetails);
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Throwable $error)
    {
        // $message = $error->getMessage()."\r\n".'
        // Line# '. $error->getLine() .'\r\n'. '
        // File Location '.$error->getFile();

        $this->logger->error($error->getMessage());

        return parent::__invoke($request, $response, $error);
    }
}
