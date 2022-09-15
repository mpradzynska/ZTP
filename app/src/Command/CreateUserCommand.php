<?php

/**
 * Create user command.
 */

namespace App\Command;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Create user command class.
 */
#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    /**
     * @param UserRepository              $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private UserRepository $userRepository, private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    protected static $defaultName = 'app:create-user';

    /**
     * Configure arguments.
     */
    protected function configure(): void
    {
        $this
            // configure an argument
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
        ;
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return int exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
        $email = $input->getArgument('email');

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $passwordQuestion = new Question('password: ');

        $plainPassword = $helper->ask($input, $output, $passwordQuestion);

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([UserRole::ROLE_ADMIN->value]);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );

        $this->userRepository->save($user, true);

        $output->write('User created');

        return Command::SUCCESS;
    }
}
