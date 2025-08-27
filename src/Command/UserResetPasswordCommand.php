<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:reset-password',
    description: 'Reset user password',
)]
class UserResetPasswordCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::OPTIONAL, 'Username for user to reset password')
            ->addOption('temp', 't', InputOption::VALUE_NONE, 'User will be forced to change this password at the first connexion')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');

        if (!$username) {
            $question = new Question('Indiquer un nom d\'utilisateur');
            $question->setAutocompleterCallback(
                fn(string $userInput): array => $this->userRepository->autocompleteUsernames($userInput)
            );
            $username = $io->askQuestion($question);
        }
        $user = $this->userRepository->findOneBy(['email' => $username]);

        if (!$user) {
            $io->warning('Utilisateur non trouvé');

            return Command::FAILURE;
        }

        $password = $io->askHidden('Mot de passe');

        $sw = new Stopwatch();

        $sw->start('validation');
        $violations = $this->validator->validate($password, [
            new PasswordStrength(),
            new NotCompromisedPassword(),
        ]);
        $event = $sw->stop('validation');
        if ($output->isVerbose()) {
            $io->info('Validation time: '.$event->getDuration().' ms.');
        }

        if (0 < $violations->count()) {
            foreach ($violations as $violation) {
                $io->error($violation->getMessage());
            }

            return Command::FAILURE;
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        if ($input->getOption('temp')) {
            // mark user to change password
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
