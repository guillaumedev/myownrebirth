<?php

namespace Gdev\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GdevUserBundle extends Bundle
{
	public function getParent()
  	{
    	return 'FOSUserBundle';
  	}
}