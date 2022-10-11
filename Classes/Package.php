<?php
declare(strict_types=1);

namespace Shel\Neos\Video;

/**
 * This file is part of the Shel.Neos.Video package.
 *
 * (c) 2022 Sebastian Helzle
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\Node;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Shel\Neos\Video\Service\VideoThumbnailService;

class Package extends BasePackage
{
    public function boot(Bootstrap $bootstrap): void
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();

        $dispatcher->connect(
            Node::class,
            'nodePropertyChanged',
            VideoThumbnailService::class,
            'updateVideoThumbnail'
        );
    }
}
