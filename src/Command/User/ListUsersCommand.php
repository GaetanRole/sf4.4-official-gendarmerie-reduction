<?php

declare(strict_types=1);

namespace App\Command\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * User Command Class that lists all existing users.
 * To use this command, open a terminal and execute the following:
 *
 *     $ php bin/console app:list-users
 *
 * See https://symfony.com/doc/current/cookbook/console/console_command.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ListUsersCommand extends Command
{
    /**
     * Command used in console.
     *
     * @var string
     */
    protected static $defaultName = 'app:list-users';

    /** @var int */
    private const MAX_RESULT_DEFAULT_OPTION = 50;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Lists all the existing users.')
            ->setHelp(
                'The <info>%command.name%</info> command lists all the users in DB:
                <info>php %command.full_name%</info>
                By default the command only displays the 50 most recent users.
                Set the number of
                results to display with the <comment>--max-results</comment> option:
                <info>php %command.full_name%</info> 
                <comment>--max-results=2000</comment>'
            )
            ->addOption(
                'max-results',
                null,
                InputOption::VALUE_OPTIONAL,
                'Limits the number of users listed.',
                self::MAX_RESULT_DEFAULT_OPTION
            )
        ;
    }

    /**
     * This method is executed after initialize(). It contains the logic to execute.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersAsPlainArrays = array_map(
            static function (User $user) {
                return [$user->getId(), $user->getUsername(), implode(', ', $user->getRoles())];
            },
            $this->userRepository->findBy([], ['id' => 'DESC'], $input->getOption('max-results'))
        );

        $io = new SymfonyStyle($input, $output);
        $io->title('Current Users present in DB:');
        $io->table(['ID', 'Username', 'Roles'], $usersAsPlainArrays);

        return 0;
    }
}
