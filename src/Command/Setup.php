<?php

namespace Xantios\Lamp\Command;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Xantios\Lamp\Config;
use Xantios\Lamp\Hue\ApiClient;
use Xantios\Lamp\Hue\Hub;
use Xantios\Lamp\Hue\HubArray;
use Xantios\Lamp\Types\ipv4;

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

            try {
                $hubs = $this->hub->search();
            } catch (GuzzleException $e) {
                $output->writeln("<error>Cant reach Philips API servers.</error>");
                return Command::FAILURE;
            } catch (\JsonException $e) {
                $output->writeln("<error>API server spat out gibberish.</error>");
                return Command::FAILURE;
            } catch (\Exception $e) {
                $output->writeln("<error>Something went wrong, please try again.</error>");
                return Command::FAILURE;
            }

            if (count($hubs) <= 0) {
                $output->writeLn("<error>No hubs found, please make sure its on and ready</error>");
                return Command::FAILURE;
            }

            $hub = $this->selectHub($hubs, $input, $output);
            if ($hub === "") {
                $output->writeLn("<error>No hub selected</error>");
                return Command::INVALID;
            }
        }

        $output->writeln("<info>Connected to $hub Please press the link button on your hub.</info>");

        $client = new ApiClient(new ipv4($hub));
        do {
            $register = $client->register();
            sleep(1);
            $output->write("<info>.</info>");
        } while($register->error === true);
        $output->writeln("");

        $this->config->username = $register->data->success->username;
        $this->config->hub = new ipv4($hub);
        $path = $this->config->store();

        $output->writeln("<info>Linked successfully! The config is stored to $path</info>");

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