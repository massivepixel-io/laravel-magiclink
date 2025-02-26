<?php

namespace MagicLink\Controllers;

use Illuminate\Routing\Controller;
use MagicLink\MagicLink;

class MagicLinkController extends Controller
{
    public function access($token)
    {
        return static::getMagicLinkByToken($token)->run();
    }
}
