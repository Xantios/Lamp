<?php

namespace Xantios\Lamp\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Xantios\Lamp\Config;
use Xantios\Lamp\Hue\Hub;
use Xantios\Lamp\Hue\HubArray;

class Setup extends Command
{
    protected static $defaultName = 'setup';
    private Hub $hub;
    private Config $config;

    protected function configure(): void
    {
        $this->hub = new Hub();
        $this->config = new Config();

        $this->addArgument('hub', InputArgument::OPTIONAL, 'Hub to connect to?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hub = $input->getArgument('hub');
        if($hub) {
            $output->writeln("<info>Using defined IP ${hub}</info>");
        } else {

            $hubs = $this->hub->search();
            if (count($hubs) <= 0) {
                $output->writeLn("<error>No hubs found</error>");
                return Command::FAILURE;
            }

            $hub = $this->selectHub($hubs, $input, $output);
            if ($hub === "") {
                $output->writeLn("<error>No hub selected</error>");
                return Command::INVALID;
            }
        }

        $output->writeln("<error>blaat ${hub}</error>");

        return Command::SUCCESS;
    }

    private function selectHub(HubArray $hubs, InputInterface $input, OutputInterface $output): string
    {
        if (count($hubs) <= 0) {
            return "";
        }

        if (count($hubs) === 1) {
            return (string)$hubs[0]->ip;
        }

        for ($i = 0; $i <= count($hubs) - 1; $i++) {
            $hub = $hubs[$i];
            $output->writeLn(sprintf("<info>Found hub @ %s</info>", (string)$hub->ip));
        }

        $output->writeLn("Found multiple hubs, please rerun this script with the specified ip. eg: ./run setup 10.0.0.1");
        return "";
    }
}