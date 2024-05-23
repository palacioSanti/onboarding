<?php namespace Acme;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use GuzzleHttp\Client;

class ShowCommand extends Command {

    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Searches MoviePedia API for a movie and displays the results in a table')
            ->addArgument('query', InputArgument::REQUIRED, 'The movie to search for');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $this->showTasks($input, $output);
    }

    public function showTasks(InputInterface $input, OutputInterface $output) {
        $movieName = $input->getArgument('query');

        $client = new Client();
        $response = $client->request('GET', "http://www.omdbapi.com/?apikey=c3c73e8d&t={$movieName}");

        $data = json_decode($response->getBody(), true);
        var_dump($data);
        if (count($data) === 0) {
            $output->writeln('No results found.');
            return 0;
        }

        $table = new Table($output);
       
        $rows = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $rows[] = [$key, $value];
        }

        $table->setRows($rows);

        $table->render();

        return 0;
    }

}

