<?php declare(strict_types=1);

namespace Frame\Http\Client\Exceptions;

use Psr\Http\Client\NetworkExceptionInterface;

final class NetworkException extends RequestException implements NetworkExceptionInterface
{
}