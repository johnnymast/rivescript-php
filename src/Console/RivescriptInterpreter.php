<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Axiom\Rivescript\Console;

use Axiom\Rivescript\Events\Event;
use Axiom\Rivescript\Exceptions\ParseException;
use Axiom\Rivescript\Rivescript;
use Axiom\Rivescript\Utils\Misc;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * RivescriptInterpreter class
 *
 * This class handles input from the interactive console from
 * the command-line.
 *
 * PHP version 8.0 and higher.
 *
 * @category Core
 * @package  Console
 * @author   Shea Lewis <shea.lewis89@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/axiom-labs/rivescript-php
 * @since    0.3.0
 */
class RivescriptInterpreter extends Command
{
    /**
     * @var Rivescript
     */
    protected Rivescript $rivescript;

    /**
     * Create a new ChatCommand instance.
     *
     * @param Rivescript $rivescript The Rivescript client.
     */
    public function __construct(Rivescript $rivescript)
    {
        $this->rivescript = $rivescript;

        parent::__construct();
    }

    /**
     * Configure the console command.
     *
     * @return void
     */
    public function configure(): void
    {
        $this->setName('chat')
            ->setDescription('Chat with a Rivescript instance')
            ->addArgument('source', InputArgument::REQUIRED, 'Your Rivescript source file');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function setupEvents(InputInterface $input, OutputInterface $output): void
    {
        $settings = [
            Event::SAY => 'fg=black;bg=blue',
            Event::DEBUG => 'fg=green',
            Event::DEBUG_WARNING => 'fg=black;bg=yellow',
            Event::DEBUG_VERBOSE => 'fg=cyan',
            Event::DEBUG_ERROR => 'fg=white;bg=red',
        ];

        $io = new SymfonyStyle($input, $output);

        foreach ($settings as $event => $style) {
            $this->rivescript->on($event, fn(string $msg) => $io->writeln("<{$style}>{$msg}</>"));
        }
    }

    /**
     * Execute the console command.
     *
     * @param InputInterface  $input  The input interface the message came from.
     * @param OutputInterface $output The output interface to output the response to.
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {



            // FIXME: Globals arnt available here since the script has not been loaded yet. So debugmode and verbose always show as false


            $this->setupEvents($input, $output);

            ob_start();

            $this->rivescript->load($input->getArgument('source'));
            $syntax = ob_get_contents();
            ob_end_clean();

            $source = basename($input->getArgument('source'));
            $debugMode = (synapse()->memory->global()->get('debug')) ? "On" : "Off";
            $verboseMode = (synapse()->memory->global()->get('verbose')) ? "On" : "Off";

            $motd = <<<EOF
                RiveScript Interpreter (PHP) -- Interactive Console v2.0
                --------------------------------------------------------
                RiveScript Version:       2.0
                Currently Loaded Source:  :source
                Debug mode: :debugmode
                Verbose mode: :verbosemode
                \n
                You are now chatting with a RiveScript bot. Type a message and press Return
                to send it. When finished, type "/quit" to exit the interactive console.
                \n
            EOF;

            $motd = Misc::formatString($motd, [
                'source' => $source,
                'debugmode' => $debugMode,
                'verbosemode' => $verboseMode,
            ]);

//            $output->writeln('RiveScript Interpreter (PHP) -- Interactive Console v2.0');
//            $output->writeln('--------------------------------------------------------');
//            $output->writeln('RiveScript Version:       2.0');
//            $output->writeln('Currently Loaded Source:  ' . $source);
//            $output->writeln('Debug mode: ' . $debugMode);
//            $output->writeln('Verbose mode: ' . $verboseMode);
//
//            $output->writeln('');
//            $output->writeln('You are now chatting with a RiveScript bot. Type a message and press Return');
//            $output->writeln('to send it. When finished, type "/quit" to exit the interactive console.');
//            $output->writeln('');

            $output->write($motd);
//            $output->write($syntax);


            $this->waitForUserInput($input, $output);


        } catch (ParseException $e) {

            $msg = $e->getMessage();
            $error = "<error>{$msg}</error>";

            $this->rivescript->error($msg);
            $output->writeln($error);
        }

        return 0;
    }

    /**
     * Wait and listen for user input.
     *
     * @param InputInterface  $input  The input interface the message came from.
     * @param OutputInterface $output The output interface to output the response to.
     *
     * @return void
     */
    protected function waitForUserInput(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $question = new Question('<info>You > </info>');

        $message = $helper->ask($input, $output, $question);

        $this->listenForConsoleCommands($input, $output, $message);

        $this->getBotResponse($input, $output, $message);
    }

    /**
     * Listen for console commands before passing message to interpreter.
     *
     * @param InputInterface  $input   The input interface the message came from.
     * @param OutputInterface $output  The output interface to output the response to.
     * @param string          $message The message typed in the console.
     *
     * @return int
     */
    protected function listenForConsoleCommands(InputInterface $input, OutputInterface $output, string $message): int
    {
        if ($message === '/quit') {
            $output->writeln('Exiting...');
            die();
        }

        if ($message === '/reload') {
            return $this->execute($input, $output);
        }

        if ($message === '/help') {
            $output->writeln('');
            $output->writeln('<comment>Usage:</comment>');
            $output->writeln('  Type a message and press Return to send.');
            $output->writeln('');
            $output->writeln('<comment>Commands:</comment>');
            $output->writeln('  <info>/help</info>        Show this text');
            $output->writeln('  <info>/reload</info>      Reload the interactive console');
            $output->writeln('  <info>/quit</info>        Quit the interative console');
            $output->writeln('');

            $this->waitForUserInput($input, $output);
        }

        return 0;
    }

    /**
     * Pass along user message to interpreter and fetch a reply.
     *
     * @param InputInterface  $input   The input interface the message came from.
     * @param OutputInterface $output  The output interface to output the response to.
     * @param string          $message The message typed in the console.
     *
     * @return void
     */
    protected function getBotResponse(InputInterface $input, OutputInterface $output, string $message): void
    {
        $bot = 'Bot > ';
        $reply = $this->rivescript->reply($message);
        $response = "<info>{$reply}</info>";

        $output->writeln($bot . $response);

        $this->waitForUserInput($input, $output);
    }
}
