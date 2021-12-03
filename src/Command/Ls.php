<?php

namespace Xantios\Lamp\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Xantios\Lamp\Config;
use Xantios\Lamp\Hue\ApiClient;
use Xantios\Lamp\Hue\Hub;
use Xantios\Lamp\Types\Light;
use Xantios\Lamp\Types\LightGroup;

class Ls extends Command
{
    protected static $defaultName = 'ls';
    private ApiClient $client;

    private array $groups;
    private array $lights;

    protected function configure(): void
    {
        $conf = new Config;
        $this->client = new ApiClient($conf->hub,$conf->username);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lights = $this->client->ls();
        $groups = $this->client->groups()->data;
        $preg = "(Group\s?\d+)"; // Filter out 'system' groups

        // Normalize groups
        foreach($groups as $group) {
            $void = [];
            if(preg_match($preg,$group->name,$void)) {
                continue;
            }

            $groupItem =  new LightGroup();
            $groupItem->lights = $group->lights;
            $groupItem->name = $group->name;

            $this->groups[] = $groupItem;
        }

        foreach($lights->data as $index => $lightData) {

            // dd($lightData);

            $light = new Light();
            $light->name = $lightData->name;
            $light->on = $lightData->state->on;
            $light->brightness = $lightData->state->bri;
            $light->x = $lightData->state->xy[0];
            $light->y = $lightData->state->xy[1];

            // Assign to group
            foreach($this->groups as $group) {
                if(in_array($index,$group->lights, true)) {
                    // printf("%s is in %s\n",$light->name,$group->name);
                    $light->group = $group->name;
                }
            }

            $this->lights[] = $light;
        }


        $table = new Table($output);
        $table
            ->setHeaders(["Name","Room","Status","Brightness","Colour"])
            ->setStyle('box-double');

        foreach ($this->lights as $item) {

            $rgb = $item->GetColour();
            $colourCode = sprintf("#%s",implode("",$rgb));

            $table->addRow([
                $item->name,
                $item->group,
                ($item->on===true)?"On":"Off",
                $item->brightness,
                "<bg=$colourCode;fg=gray> ".$colourCode." </>",
            ]);
        }
        
        $table->render();

        return Command::SUCCESS;
    }
}