<?php

namespace App\Command;

use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;

class MailerHosNewsletterSendCommand extends Command
{
    protected static $defaultName = 'mailer:hos:newsletter:send';

    private $twig;
    private $gmailUsername;
    private $gmailPassword;

    public function __construct(Environment $twig, string $gmailUsername, string $gmailPassword)
    {
        parent::__construct(null);

        $this->twig = $twig;
        $this->gmailUsername = $gmailUsername;
        $this->gmailPassword = $gmailPassword;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $transport = new GmailSmtpTransport($this->gmailUsername, $this->gmailPassword);
        $mailer = new Mailer($transport);

        $email = (new TemplatedEmail())
            ->from('mwalczak@internationalfx.com')

            ->to(
                'walczak.mac@gmail.com',
                'magdalena.walczuk@agencjahagen.pl',
                'walczukmagda@wp.pl',
//                'mwalczak@ifxpayments.com',
                'joanna.zoltkowska@agencjahagen.pl'
            )

            ->subject('HoS newsletter')
            ->htmlTemplate('emails/hos_feb_2020.html.twig')
        ;

//        $content = $this->twig->render('emails/hos.html.twig', [
//            'email' => new WrappedTemplatedEmail($this->twig, $email)
//        ]);

        $renderer = new BodyRenderer($this->twig);
        // this updates the $email object contents with the result of rendering
        // the template defined earlier with the given context
        $renderer->render($email);

        $mailer->send($email);

        $output->writeln('all good');

        return 0;
    }
}
