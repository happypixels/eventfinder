<?php

namespace App\Agents;

use ReflectionClass;

class BaseAgent
{
    /**
     * The name of the agent.
     *
     * @var string
     */
    public $name;

    /**
     * A slugged version of the name used for identification.
     *
     * @var string
     */
    public $identifier;

    /**
     * The website of the agent.
     *
     * @var string
     */
    public $website;

    /**
     * The class constructor.
     */
    public function __construct()
    {
        $this->name         = $this->name();
        $this->identifier   = $this->identifier();
        $this->website      = $this->config('website');
    }

    /**
     * Returns a slugged version of the agent name.
     *
     * @return string
     */
    public function identifier() : string
    {
        return str_slug($this->name);
    }

    /**
     * Returns the name of the agent. Defaults to the class name.
     *
     * @return string
     */
    public function name() : string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Returns the fully qualified class name (including namespace) for the class.
     *
     * @return string
     */
    public function fullyQualifiedClassName() : string
    {
        return get_class($this);
    }

    /**
     * Returns the trackback URL for the agent.
     *
     * @return string
     */
    public function trackback() : string
    {
        return $this->config('trackback');
    }

    /**
     * Cleans up after each sync if necessary. Sometimes we have to store temp files while parsing the
     * data, this method cleans it all up afterwards.
     *
     * @return void
     */
    public function cleanup() : void
    {
        //
    }

    /**
     * Returns the selected configuration value for the agent.
     *
     * @param string $key
     * @return mixed
     */
    public function config($key)
    {
        return config('agents.' . $this->identifier() . '.' . $key, '');
    }

    /**
     * Builds a standardised filename for an event, based on the eventname and the correct extension.
     *
     * @param string $eventName
     * @param string $url
     * @return string
     */
    public function buildEventFilename($eventName, $url)
    {
        return str_slug($eventName) . '.' . pathinfo($url, PATHINFO_EXTENSION);
    }
}
