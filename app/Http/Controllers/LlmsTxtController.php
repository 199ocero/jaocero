<?php

namespace App\Http\Controllers;

use App\Support\Seo\LlmsTxt;
use Illuminate\Http\Response;

class LlmsTxtController extends Controller
{
    /**
     * /llms.txt: an index of the site for LLMs.
     */
    public function index(): Response
    {
        return $this->markdown(LlmsTxt::index());
    }

    /**
     * /llms-full.txt: all portfolio content in one markdown document.
     */
    public function full(): Response
    {
        return $this->markdown(LlmsTxt::full());
    }

    private function markdown(string $content): Response
    {
        return response($content)->header('Content-Type', 'text/markdown; charset=UTF-8');
    }
}
