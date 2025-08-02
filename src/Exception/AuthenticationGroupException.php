<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationGroupException extends AuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Vous n\'avez pas accès à l\'application, merci de contacter un administrateur.';
    }
}
