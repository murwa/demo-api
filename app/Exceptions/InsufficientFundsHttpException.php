<?php
/**
 * Created by PhpStorm.
 * User: mxgel
 * Date: 5/3/17
 * Time: 10:36 PM
 */

namespace App\Exceptions;


use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

/**
 * Class InsufficientFundsHttpException
 *
 * @package App\Exceptions
 */
class InsufficientFundsHttpException extends PreconditionFailedHttpException
{
    /**
     * InsufficientFundsHttpException constructor.
     *
     * @param null            $message
     * @param \Exception|null $previous
     * @param int             $code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct($message ?: 'Insufficient funds', $previous, $code);
    }

}