<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * User Command Class that lists all existing users.
 * To use this command, open a terminal window, enter into your project directory
 * and execute the following:
 *
 *     $ php bin/console app:list-users
 *
 * See https://symfony.com/doc/current/cookbook/console/console_command.html
 * For more advanced uses, commands can be defined as services too. See
 * https://symfony.com/doc/current/console/commands_as_services.html
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

    /**
     * All given users.
     *
     * @var UserRepository
     */
    private $users;

    public function __construct(UserRepository $users)
    {
        parent::__construct();

        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Lists all the existing users')
            ->setHelp(
                'The <info>%command.name%</info> command lists all the users in DB :
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
                'Limits the number of users listed',
                50
            );
    }

    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $maxResults = $input->getOption('max-results');
        $allUsers = $this->users->findBy([], ['id' => 'DESC'], $maxResults);

        $usersAsPlainArrays = array_map(
            static function (User $user) {
                return [$user->getId(), $user->getUsername(), implode(', ', $user->getRoles())];
            },
            $allUsers
        );

        $bufferedOutput = new BufferedOutput();
        $io = new SymfonyStyle($input, $bufferedOutput);
        $io->title('Current Users present in DB :');
        $io->table(['ID', 'Username', 'Roles'], $usersAsPlainArrays);

        $usersAsATable = $bufferedOutput->fetch();
        $output->write($usersAsATable);
    }
}
