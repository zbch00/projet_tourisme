<?php

namespace App\Command;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;
use League\Csv\Statement;


// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:import-villes-franche-comte')]
class ImporterVillesFrancheComteCommand extends Command
{
    private EntityManagerInterface $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $stream = fopen('documentation/villes.csv', 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);
        $stmt = Statement::create();
        $records = $stmt->process($csv);

        foreach ($records as $record) {

            if ($record["Département"]==25 || $record["Département"]==39 || $record["Département"]==70 || $record["Département"]==90){
                $ville = new Ville();
                if(empty($record["Ancienne commune"])){
                    $ville->setNom($record["Commune"]);
                }else {
                    $ville->setNom($record["Commune"]." ".$record["Ancienne commune"]);
                }
                $ville->setCp($record["Code postal"]);
                $ville->setNomDep($record["Nom département"]);
                $ville->setNumDep($record["Département"]);
                $ville->setNomRegion($record["Région"]);
                $this->manager->persist($ville);

            }
        }
        $this->manager->flush();

        return Command::SUCCESS;

    }
}
