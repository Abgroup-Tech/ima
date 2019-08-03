<?php

namespace Kbh\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KbhUserBundle extends Bundle
{
    public function getParent() {
        return 'FOSUserBundle';
    }
}
