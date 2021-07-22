<?php

namespace App\Exceptions;

use Exception;

/**
 * Класс исключения, связанный с основной работой приложения.
 * <p>
 * Все исключения такого типа должны логироваться с типом error.
 */
class ApplicationException extends Exception
{
}
