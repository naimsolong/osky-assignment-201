<?php

namespace Osky\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Question\Question;

class RedditSearchCmd extends Command {
    protected static $defaultName = 'reddit:search';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new user.')
    
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        
        $output->writeln([
            'Reddit Search v0.1.0',
            '====================',
            '',
        ]);

        $question = new Question('Please enter the name of the subreddit (default:webdev): ', 'webdev');    
        $subreddit = $helper->ask($input, $output, $question);

        $question = new Question('Please enter the search term (default:php): ', 'php');    
        $term = $helper->ask($input, $output, $question);

        $output->writeln('Subreddit => '.$subreddit);
        $output->writeln('Term => '.$term);
        
        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}