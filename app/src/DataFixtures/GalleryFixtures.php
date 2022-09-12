<?php
/**
 * Gallery fixtures
 */

namespace App\DataFixtures;

use App\Entity\Gallery;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class GalleryFixtures
 */
class GalleryFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public const GROUP_NAME = 'galleries';

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    /**
     * Load data.
     */
    protected function loadData(): void
    {
        $this->createMany(10, self::GROUP_NAME, function ($i) {
            $gallery = new Gallery();
            $gallery->setName(sprintf('Gallery-%d', $i));
            $gallery->setUser($this->getRandomReference(
                UserFixtures::GROUP_NAME,
            ));

            return $gallery;
        });

        $this->manager->flush();
    }
}
