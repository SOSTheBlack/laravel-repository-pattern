<?php

namespace SOSTheBlack\Repository\Generators\Commands;

use Illuminate\Console\Command;
use SOSTheBlack\Repository\Generators\FileAlreadyExistsException;
use SOSTheBlack\Repository\Generators\TransformerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class TransformerCommand.\Commands
 */
class TransformerCommand extends Command
{

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'make:transformer';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new transformer.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Transformer';

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
            (new TransformerGenerator([
                'name' => $this->argument('name'),
                'force' => $this->option('force'),
            ]))->run();
            $this->info("Transformer created successfully.");
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
                'The name of model for which the transformer is being generated.',
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
