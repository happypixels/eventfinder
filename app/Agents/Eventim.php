<?php

namespace App\Agents;

use App\Contracts\Agent;
use GuzzleHttp\Client;
use \File;
use ZipArchive;
use SimpleXMLElement;
use Illuminate\Support\Facades\Storage;

class Eventim extends BaseAgent implements Agent
{
    /**
     * {@inheritdoc}
     */
    public function gatherEvents() : array
    {
        if (!($data = $this->downloadAndReadXML())) {
            return [];
        }

        $events = [];

        foreach ($data->eventserie as $eventSerie) {
            if (!isset($eventSerie->event) || !count($eventSerie->event)) {
                continue;
            }

            // Manage single events as a group containing only one event, for the loop.
            if (is_object($eventSerie->event)) {
                $xmlEvents = [$eventSerie->event];
            } else {
                $xmlEvents = $eventSerie->event;
            }

            foreach ($xmlEvents as $eventData) {
                $eventData->imageUrl = $this->parseImageUrl($eventSerie);
                $eventData->estext   = $eventSerie->estext;

                $events[] = $eventData;
            }
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function mapEvent($event) : array
    {
        return [
            'agent_class'     => $this->fullyQualifiedClassName(),
            'agent_event_id'  => $event->eventid,
            'title'           => $event->eventname,
            'description'     => $event->estext,
            'url'             => $event->eventlink,
            'event_starts_at' => $event->eventdate . ' ' . $event->eventtime,
            'is_cancelled'    => 0,
            'is_sold_out'     => 0,
            'sale_starts_at'  => date('Y-m-d', strtotime($event->onsaledate)) . ' ' . $event->onsaletime,
            'sale_ends_at'    => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function mapVenue($event) : array
    {
        return [
            'title'           => $event->eventvenue,
            'city'            => $event->eventplace,
            'address'         => $event->eventstreet,
            'zipcode'         => $event->eventzip,
            'latitude'        => 0,
            'longitude'       => 0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function downloadAndMapImage($event) : array
    {
        try {
            $targetPath = storage_path() . '/app/events/' . $this->identifier() . '/' . $event->eventid;

            // Set the image name with extension.
            $imageName = $this->buildEventFilename($event->eventname, $event->imageUrl);

            // Get the actual image.
            $eventImage = file_get_contents($event->imageUrl);

            // Create the image folder if it doesn't exist.
            Storage::makeDirectory('events/' . $this->identifier() . '/' . $event->eventid);

            // Save the image.
            File::put($targetPath . '/' . $imageName, $eventImage);

            return ['image' => $imageName, 'image_url' => $event->imageUrl];
        } catch (\Exception $e) {
            return ['image' => '', 'image_url' => ''];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mapPrices($event) : array
    {
        $prices         = ['min_price' => 0, 'min_price' => 0];
        $amountOfPrices = count($event->pricekategory);

        if ($amountOfPrices > 1) {
            $tmpPrices = [];
            for ($i = 0; $i < $amountOfPrices; $i++) {
                $tmpPrices[] = intval($event->pricekategory[$i]->price);
            }

            $prices['min_price'] = min($tmpPrices);
            $prices['max_price'] = max($tmpPrices);
        } else {
            $prices['min_price'] = $event->pricekategory->price;
            $prices['max_price'] = $event->pricekategory->price;
        }

        return $prices;
    }

    /**
     * {@inheritdoc}
     */
    public function cleanup() : void
    {
        Storage::deleteDirectory('temp/' . $this->identifier());
    }

    /**
     * Downloads the latest zip file from Eventim, unzips it and returns the contained XML file contents.
     *
     * @return mixed
     */
    private function downloadAndReadXML()
    {
        $url        = 'http://feeds.eventim.com/export/users/Adtraction/latest.zip';
        $path       = storage_path() . '/app/temp/' . $this->identifier();

        // Create the target folder if it doesn't exist.
        Storage::makeDirectory('temp/' . $this->identifier());

        $response = (new Client())->get($url, ['auth' => [$this->config('username'), $this->config('password')]]);

        file_put_contents($path . '/latest.zip', $response->getBody()->getContents());

        // Unzip .xml-file from .zip-file.
        $zip = new ZipArchive;
        $zip->open($path . '/latest.zip');
        $zip->extractTo($path . '/');
        $zip->close();

        $file = collect(scandir($path))->filter(function ($file) {
            return (ends_with($file, '.xml'));
        })->first();

        // Extract XML from file.
        $data = new SimpleXMLElement(file_get_contents($path . '/' . $file), LIBXML_NOCDATA);

        if (!count($data->eventserie)) {
            return false;
        }

        return $data;
    }

    /**
     * Reads in which size an event image is available. Attempts to use the biggest first.
     *
     * @return mixed
     */
    private function parseImageUrl($eventSerie)
    {
        if ($eventSerie->espicture_big || $eventSerie->espicture || $eventSerie->espicture_small) {
            if ($eventSerie->espicture_big) {
                return $eventSerie->espicture_big;
            } elseif ($eventSerie->espicture) {
                return $eventSerie->espicture;
            } else {
                return $eventSerie->espicture_small;
            }
        }

        return null;
    }
}
