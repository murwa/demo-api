<?php
/**
 * Created by PhpStorm.
 * User: mxgel
 * Date: 5/4/17
 * Time: 4:38 AM
 */

namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Class ATMException
 *
 * @package App\Exceptions
 */
class ATMException extends \RuntimeException implements HttpExceptionInterface
{
    public function __construct($message = null, \Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($message, $code, $previous);
        $this->headers = $headers;
    }

    /**
     * @var int
     */
    protected $statusCode = 412;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param int $statusCode
     *
     * @return ATMException
     */
    public function setStatusCode(int $statusCode): ATMException
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return ATMException
     */
    public function setHeaders(array $headers): ATMException
    {
        $this->headers = $headers;

        return $this;
    }
}