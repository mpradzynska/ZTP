<?php
/**
 * Comment fixtures
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public const GROUP_NAME = 'comments';

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [ImageFixtures::class];
    }

    /**
     * Load data.
     */
    protected function loadData(): void
    {
        $this->createMany(50, self::GROUP_NAME, function ($i) {
            $comment = new Comment();
            $comment->setEmail($this->faker->email());
            $comment->setNick($this->faker->name());
            $comment->setText($this->faker->text(maxNbChars: 500));
            $comment->setImage(
                $this->getRandomReference(
                    ImageFixtures::GROUP_NAME,
                )
            );

            return $comment;
        });

        $this->manager->flush();
    }
}
