<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $tag1 = Tag::create(['name' => 'tag 1']);
        $tag2 = Tag::create(['name' => 'tag 2', 'color' => '#FF0000']);
        $tag3 = Tag::create(['name' => 'tag 3', 'color' => '#615fff']);

        Event::create([
            'name' => 'Date',
            'description' => 'The Timeline/Graph2D is an interactive visualization chart to visualize data in time. The data items can take place on a single date, or have a start and end date (a range). You can freely move and zoom in the timeline by dragging and scrolling in the Timeline. Items can be created, edited, and deleted in the timeline. The time scale on the axis is adjusted automatically, and supports scales ranging from milliseconds to years.',
            'date' => '1910-03-25 12:00',
        ])->tags()->attach([$tag1->id, $tag2->id]);

        Event::create([
            'name' => 'Autre date',
            'description' => 'euuuh...',
            'date' => '2025-03-25 16:20',
        ])->tags()->attach($tag3->id);

        Event::create([
            'name' => 'Date intermédiaire',
            'description' => 'Au milieu quoi.',
            'date' => '1970-06-05 03:10',
        ]);
    }
}
