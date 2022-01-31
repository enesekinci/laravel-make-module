<?php

namespace EnesEkinci\LaravelMakeModule\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {module} {--target=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $moduleName = $this->argument('module');
        $target = $this->options()['target'][0] ?? '';

        $controller = $moduleName . 'Controller';

        $controller = Str::ucfirst($target) . '/' . $controller;

        $model = $moduleName;

        $migration = 'create_' . Str::pluralStudly(Str::snake($moduleName)) . '_table';

        $viewFiles = ['index', 'create', 'edit', 'show'];

        foreach ($viewFiles as $view) {
            $view = $target . '.' . Str::pluralStudly(Str::kebab($moduleName)) . '.' . $view;

            $path = $this->viewPath($view);

            $this->createDir($path);

            if (File::exists($path)) {
                $this->error("File {$path} already exists!");
            } else {
                File::put($path, 'Hello World! <br/> ' . $moduleName . ' / ' . $view);
                $this->info("File {$path} created.");
            }
        }

        $this->call('make:controller', ['name' => $controller]);
        $this->call('make:model', ['name' => $model]);
        $this->call('make:migration', ['name' => $migration]);

        return 0;
    }

    public function viewPath($view)
    {
        $view = str_replace('.', '/', $view) . '.blade.php';

        $path = "resources/views/{$view}";

        return $path;
    }

    /**
     * Create view directory if not exists.
     *
     * @param $path
     */
    public function createDir($path)
    {
        $dir = dirname($path);

        if (!File::exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
