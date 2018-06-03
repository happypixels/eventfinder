<?php

namespace App\Agents;

class Kernel
{
    /**
     * The currently active ticket agents.
     *
     * @var array
     */
    protected static $agents = [
        \App\Agents\Eventim::class
    ];

    /**
     * Returns all agents defined in the $agents array.
     *
     * @return array
     */
    public static function get()
    {
        return self::$agents;
    }
}
