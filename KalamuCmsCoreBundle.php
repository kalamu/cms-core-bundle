<?php

/*
 * This file is part of the kalamu/cms-core-bundle package.
 *
 * (c) ETIC Services
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kalamu\CmsCoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kalamu\CmsCoreBundle\DependencyInjection\Compiler\ContentTypeCompilerPass;

class KalamuCmsCoreBundle extends Bundle
{

    public function build(ContainerBuilder $container) {
        parent::build($container);

        $container->addCompilerPass(new ContentTypeCompilerPass());
    }

}
