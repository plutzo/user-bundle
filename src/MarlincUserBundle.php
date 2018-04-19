<?php

namespace Marlinc\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarlincUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataUserBundle';
    }
}