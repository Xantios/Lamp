<?php

namespace Xantios\Lamp\Hue;

class IndentyResolver {

    /**
     * Accept a global name (group or single light) and resolves to array
     *
     * @returns int[]
     */
    public static function resolve(string $identifier,ApiClient $client):array {

        $lights = [];
        $useGroup = false;

        $groups = $client->groups()->data;

        foreach($groups as $group) {
            if($group->name === $identifier) {
                $lights = $group->lights;
                $useGroup = true;
            }
        }

        if(!$useGroup) {
            $lightData = $client->ls()->data;
            foreach($lightData as $index => $item) {
                if($item->name===$identifier) {
                    $lights[0] = $index;
                }
            }
        }

        return $lights;
    }
}