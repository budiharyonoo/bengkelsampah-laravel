<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when attempting to delete the last admin of a bank sampah.
 *
 * This prevents the scenario where a bank sampah would be left without any
 * administrator access, which would be a critical operational failure.
 */
class AdminDeletionException extends Exception
{
    //
}
