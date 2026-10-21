<?php

namespace App\Exceptions;

use Exception;

/**
 * Stok qaydası pozulanda (məs: alışı silmək üçün stok kifayət etmir) atılır.
 * Ayrıca sinif olduğu üçün UI qatında yalnız bunu tutub bildirişə çeviririk.
 */
class StockException extends Exception
{
}
