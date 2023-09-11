<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\OpenApi(
 *  @OA\Info(
 *      title="Onfly API - Expenses",
 *      version="1.0.0",
 *      description="API documentation for help testing.",
 *      @OA\Contact(
 *          email="draphael48@gmail.com"
 *      )
 *  ),
 *  @OA\Server(
 *      description="API Features",
 *      url="http://localhost:9001/api"
 *  ),
 *  @OA\PathItem(
 *      path="/"
 *  )
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
