<?php

namespace Osky\Command;

use Exception;
use Osky\App\Reddit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Question\Question;

class RedditSearchCmd extends Command {
    protected static $defaultName = 'reddit:search';

    protected function configure(): void
    {
        $this->setDescription('Allow to search subreddit.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $output->writeln([
            '<fg=#ff6666>Reddit Search v0.1.0',
            '====================</>',
            '',
        ]);

        $question = new Question('Please enter the name of the subreddit (default:webdev): ', 'webdev');    
        $subreddit = strtolower(trim($helper->ask($input, $output, $question)));

        $question = new Question('Please enter the search term (default:php): ', 'php');    
        $term = strtolower(trim($helper->ask($input, $output, $question)));
        
        $output->writeln([
            '',
            'Searching for "'.$term.'" in https://reddit.com/r/'.$subreddit.'/new ...',
            '',
        ]);
        
        
        $reddit = (new Reddit($subreddit, $term))->generateToken()->search()->sort()->trim()->get(['created', 'title', 'url', 'selftext']);
        
        if($reddit->_count == 0)
        {
            $message = !empty($reddit->_error) ? $reddit->_error : 'No data available!';
            $output->writeln('<fg=white;bg=#ff6666;>ERROR! '.$message."</>");

            return Command::FAILURE;
        }

        $table = new Table($output);
        $table->setHeaders(['Date', 'Title', 'Url', 'Excerpt'])->setRows($reddit->_data);
        $table->render();
        return Command::SUCCESS;
    }
}