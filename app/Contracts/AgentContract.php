<?php

namespace App\Contracts;

interface AgentContract
{
    /**
     * Retrieves the events from the server and parse them into a single leveled array.
     *
     * @return array
     */
    public function gatherEvents() : array;

    /**
     * Returns a slugged version of the agent name.
     *
     * @return string
     */
    public function identifier() : string;

    /**
     * Returns the name of the agent.
     *
     * @return string
     */
    public function name() : string;

    /**
     * Returns the trackback URL for the agent.
     *
     * @return string
     */
    public function trackback() : string;

    /**
     * Returns the fully qualified class name (including namespace) for the class.
     *
     * @return string
     */
    public function fullyQualifiedClassName() : string;

    /**
     * Maps an event from the agent's structure into our database structure.
     *
     * @return array
     */
    public function mapEvent($event) : array;

    /**
     * Maps the venue for an event from the agent's structure into our database structure.
     *
     * @return array
     */
    public function mapVenue($event) : array;

    /**
     * Maps the prices for an event to an array containing min_price and max_price.
     *
     * @return array
     */
    public function mapPrices($event) : array;

    /**
     * Downloads the image for an event from the agent and maps it into an array containing the image name and URL.
     *
     * @return array
     */
    public function downloadAndMapImage($event) : array;

    /**
     * Cleans up after each sync if necessary. Sometimes we have to store temp files while parsing the
     * data, this method cleans it all up afterwards.
     *
     * @return void
     */
    public function cleanup() : void;
}
