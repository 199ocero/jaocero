<?php

namespace App\Http\Controllers;

use App\Support\Seo\RobotsTxt;
use Illuminate\Http\Response;

class RobotsTxtController extends Controller
{
    public function __invoke(): Response
    {
        return response(RobotsTxt::render())->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
