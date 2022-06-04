<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MakeAdminCommand extends Command
{
    protected static $defaultName = 'make-admin';
    private $entityManager;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager,string $name = null)
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create admin account')
            ->addArgument('email', InputArgument::REQUIRED, 'Enter email address')
            ->addArgument('password', InputArgument::REQUIRED, 'Enter password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $admin = new User();
        $admin->setEmail($email)
            ->setPassword(
                $this->passwordEncoder->encodePassword($admin, $password)
            )
            ->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Admin account created successfully');

        return 0;
    }
}
