<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes, Sluggable;

    protected $fillable = [
        'venue_id',
        'agent_class',
        'agent_event_id',
        'title',
        'description',
        'url',
        'image',
        'image_url',
        'min_price',
        'max_price',
        'is_cancelled',
        'is_sold_out',
        'event_starts_at',
        'sale_starts_at',
        'sale_ends_at'
    ];
    protected $hidden  = ['agent_class'];
    protected $appends = ['agent'];

    /**
     * Makes the agent available on the event object.
     *
     * @return App\Contracts\AgentContract
     */
    public function getAgentAttribute()
    {
        return $this->agent();
    }

    /**
     * Returns an instance of the ticket agent for the event.
     *
     * @return App\Contracts\AgentContract
     */
    public function agent()
    {
        return new $this->agent_class();
    }

    /**
    * Return the sluggable configuration array for this model.
    *
    * @return array
    */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
            ]
        ];
    }

    /**
    * Get the route key for the model.
    *
    * @return string
    */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
