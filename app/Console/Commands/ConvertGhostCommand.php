<?php

namespace App\Console\Commands;

use App\Converters\GhostJsonConverter;
use Illuminate\Console\Command;

class ConvertGhostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:ghost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Convert Ghost data json to the new version';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $converter = new GhostJsonConverter();
        $converter->convert();
        return 0;
    }
}
