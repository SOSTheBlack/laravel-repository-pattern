<?php

namespace SOSTheBlack\Repository\Generators\Commands;

use Illuminate\Console\Command;
use SOSTheBlack\Repository\Generators\FileAlreadyExistsException;
use SOSTheBlack\Repository\Generators\PresenterGenerator;
use SOSTheBlack\Repository\Generators\TransformerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class PresenterCommand
 * @package SOSTheBlack\Repository\Generators\Commands
 * @author Jean C. Garcia <garciasoftwares@gmail.com>
 */
class PresenterCommand extends Command
{

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'make:presenter';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new presenter.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Presenter';

    /**
     * Execute the command.
     *
     * @return void
     * @see fire()
     */
    public function handle(): void
    {
        $this->laravel->call([$this, 'fire'], func_get_args());
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function fire()
    {

        try {
            (new PresenterGenerator([
                'name' => $this->argument('name'),
                'force' => $this->option('force'),
            ]))->run();
            $this->info("Presenter created successfully.");

            if (!\File::exists(app()->path() . '/Transformers/' . $this->argument('name') . 'Transformer.php')) {
                if ($this->confirm('Would you like to create a Transformer? [y|N]')) {
                    (new TransformerGenerator([
                        'name' => $this->argument('name'),
                        'force' => $this->option('force'),
                    ]))->run();
                    $this->info("Transformer created successfully.");
                }
            }
        } catch (FileAlreadyExistsException $e) {
            $this->error($this->type . ' already exists!');

            return false;
        }
    }


    /**
     * The array of command arguments.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of model for which the presenter is being generated.',
                null
            ],
        ];
    }


    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [
            [
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ]
        ];
    }
}
