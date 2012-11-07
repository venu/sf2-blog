<?php

namespace Venu\BlogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VenuBlogBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
