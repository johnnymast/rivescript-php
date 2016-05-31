<?php

namespace Vulcan\Rivescript\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Vulcan\Rivescript\Rivescript;

class ChatCommand extends Command
{
    protected $rivescript;

    public function __construct(Rivescript $rivescript)
    {
        $this->rivescript = $rivescript;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('chat')
             ->setDescription('Load in a Rivescript brain instance to chat with')
             ->addArgument('brain', InputArgument::REQUIRED, 'Your Rivescript brain instance');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->rivescript->loadFile($input->getArgument('brain'));
        $brain = explode('/', $input->getArgument('brain'));
        $brain = end($brain);

        $output->writeln('RiveScript Interpreter (PHP) -- Interactive Console');
        $output->writeln('---------------------------------------------------');
        $output->writeln('RiveScript Version:       2.0');
        $output->writeln('Currently Loaded Brain:   '.$brain);
        $output->writeln('');
        $output->writeln('You are now chatting with the RiveScript bot. Type a message and press Return');
        $output->writeln('to send it. When finished, type "/quit" to exit the interactive console.');
        $output->writeln('');

        $this->waitForUserInput($input, $output);
    }

    protected function waitForUserInput($input, $output)
    {
        $helper   = $this->getHelper('question');
        $question = new Question('<info>You > </info>');

        $message = $helper->ask($input, $output, $question);

        $this->parseConsoleCommands($message, $output);

        $this->getBotResponse($input, $output, $message);
    }

    protected function getBotResponse($input, $output, $message)
    {
        $bot      = 'Bot > ';
        $reply    = $this->rivescript->reply(null, $message);
        $response = "<info>{$reply}</info>";

        $output->writeln($bot.$response);

        $this->waitForUserInput($input, $output);
    }

    protected function parseConsoleCommands($message, $output)
    {
        if ($message === '/quit') {
            $output->writeln('Exiting...');
            die();
        }
    }
}
