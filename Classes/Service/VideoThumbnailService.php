<?php
declare(strict_types=1);

namespace Shel\Neos\Video\Service;

/**
 * This file is part of the Shel.Neos.Video package.
 *
 * (c) 2022 Sebastian Helzle
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\TagRepository;
use Psr\Log\LoggerInterface;

/**
 * @Flow\Scope("singleton")
 */
class VideoThumbnailService
{
    public const VIDEO_NODETYPE = 'Shel.Neos.Video:Mixin.Video';
    public const YOUTUBE_BASEURL = 'https://img.youtube.com/vi/';
    public const THUMBNAIL_PROPERTY_NAME = 'thumbnail';
    const YOUTUBE_TAG = 'YouTube Thumbnails';

    /**
     * @Flow\Inject
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @Flow\Inject
     * @var TagRepository
     */
    protected $tagRepository;

    public function updateVideoThumbnail(
        NodeInterface $node,
        string $propertyName,
        $oldValue,
        $newValue
    ): void {
        if ($propertyName !== 'videoId' ||
            (!$oldValue && !$newValue) ||
            !$node->getNodeType()->isOfType(self::VIDEO_NODETYPE)) {
            $this->logger->debug('No videoId property or not a video node or value didnt change');
            return;
        }

        $thumbnail = $newValue ? $this->getThumbnail($newValue) : null;
        $node->setProperty(self::THUMBNAIL_PROPERTY_NAME, $thumbnail);
        $this->logger->debug('Updated video thumbnail', [$newValue]);
        $this->persistenceManager->persistAll();
    }

    protected function getThumbnail(string $videoId, string $quality = 'hqdefault'): ?Image
    {
        $url = self::YOUTUBE_BASEURL . $videoId . '/' . $quality . '.jpg';
        try {
            $resource = $this->resourceManager->importResource($url);
            $thumbnail = new Image($resource);
            $this->tagThumbnail($thumbnail);
            $this->logger->info('Imported thumbnail', ['videoId' => $videoId]);
            return $thumbnail;
        } catch (\Exception $e) {
            $this->logger->warning('Could not import thumbnail', ['videoId' => $videoId]);
            return null;
        }
    }

    protected function tagThumbnail(Image $thumbnail): void
    {
        $tag = $this->tagRepository->findOneByLabel(self::YOUTUBE_TAG);
        if (!$tag) {
            $tag = new Tag(self::YOUTUBE_TAG);
            $this->tagRepository->add($tag);
            $this->logger->info('Created YouTube tag');
        }
        $thumbnail->addTag($tag);
    }
}
