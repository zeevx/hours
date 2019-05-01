<?php

namespace App\Commands;

use App\Frame;
use App\Project;
use Illuminate\Support\Carbon;
use LaravelZero\Framework\Commands\Command;

class StartCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'start {project}';

    /**
     * @var string
     */
    protected $description = 'Start tracking time for the given project.';

    public function handle(): void
    {
        if ($active = Frame::active()) {
            if (! $this->confirm(
                "Time is already being tracked for {$active->project->name} (started {$active->diff()}).  ".
                    'Do you want to stop the active frame?'
            )) {
                return;
            }
            $this->call('stop');
        }

        $project = Project::firstOrCreate([
            'name' => $this->argument('project'),
        ]);

        $frame = $project->frames()->create([
            'started_at' => Carbon::now(),
        ]);

        // TODO: get the actual *users* timezone for output purposes
        $this->info("Starting {$project->name} at {$frame->started_at}");
    }
}