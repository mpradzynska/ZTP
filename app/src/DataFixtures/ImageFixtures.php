<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public const GROUP_NAME = 'images';

    private const IMAGES = [
        'https://images.pexels.com/photos/688660/pexels-photo-688660.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
        'https://images.pexels.com/photos/813872/pexels-photo-813872.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
        'https://images.pexels.com/photos/1438761/pexels-photo-1438761.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
    ];

    /**
     * Load data.
     */
    protected function loadData(): void
    {
        $this->createMany(50, self::GROUP_NAME, function ($i) {
            $image = new Image();
            $image->setTitle($this->faker->text(20));
            $image->setDescription($this->faker->text(200));
            $image->setPath(
                $this->faker->randomElement(self::IMAGES)
            );

            $image->setGallery(
                $this->getRandomReference(GalleryFixtures::GROUP_NAME)
            );

            return $image;
        });

        $this->manager->flush();
    }

    public function getDependencies(): array
    {
        return [GalleryFixtures::class];
    }
}
