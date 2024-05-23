<?php namespace Acme;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use GuzzleHttp\Client;

class ShowCommand extends Command {

    const MAX_LENGTH = 130; //Largo maximo de caracteres para que no se rompa la tabla

    protected function configure() {
        $this
            ->setName('show')
            ->setDescription('Searches MoviePedia API for a movie and displays the results in a table')
            ->addArgument('query', InputArgument::REQUIRED, 'The movie to search for')
            ->addOption('fullPlot', null, InputOption::VALUE_NONE, 'Show full plot of the movie');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $this->showTasks($input, $output);
    }

    public function showTasks(InputInterface $input, OutputInterface $output) {
        $movieName = $input->getArgument('query');
        $plot = $input->getOption('fullPlot') ? 'full' : 'short';

        $client = new Client();
        $response = $client->request('GET', "http://www.omdbapi.com/?apikey=c3c73e8d&t={$movieName}&plot={$plot}");

        $data = json_decode($response->getBody(), true);

        if (count($data) === 0) {
            $output->writeln('No results found.');
            return 0;
        }

        $table = new Table($output);
       
        $rows = $this->setRows($data);

        $table->setRows($rows);

        $table->render();

        return 0;
    }

    public function setRows(array $data): array {
        $rows = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $parts = str_split($value, $this::MAX_LENGTH);
            foreach($parts as $part) {
                $rows[] = [$key, $part];
            }
            
        }

        return $rows;
    }

}

