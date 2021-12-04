<?php

namespace Xantios\Lamp\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Xantios\Lamp\Config;
use Xantios\Lamp\Hue\ApiClient;
use Xantios\Lamp\Hue\IndentyResolver;

class On extends Command
{
    protected static $defaultName = 'on';
    private ApiClient $client;

    protected function configure(): void
    {
        $conf = new Config;
        $this->client = new ApiClient($conf->hub,$conf->username);

        $this->addArgument('identifier', InputArgument::REQUIRED, 'Name of room or device to switch');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');
        $lights = IndentyResolver::Resolve($identifier,$this->client);

        $output->writeln("<info>Switching $identifier</info>");
        foreach($lights as $light) {
            $this->client->light($light,true);
        }

        return Command::SUCCESS;
    }
}