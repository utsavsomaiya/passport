<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Closure;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:query')]
class QueryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:query {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new query';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Queries';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/query.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $name
     */
    protected function buildClass($name): string
    {
        $replace = $this->buildModelReplacements();

        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }

    /**
     * Build the model replacement values.
     *
     * @return array<string, string>
     */
    protected function buildModelReplacements(): array
    {
        /** @var string $name */
        $name = $this->argument('name');

        $modelClass = $this->parseModel(Str::before($name, 'Queries'));

        if (
            ! file_exists(app_path('Models/' . Str::of($modelClass)->classBasename() . '.php')) &&
            confirm(sprintf('A %s model does not exist. Do you want to generate it?', $modelClass), true)
        ) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        return [
            '{{ namespacedModel }}' => $modelClass,
        ];
    }

    /**
     * Get the fully-qualified model class name.
     *
     *
     * @throws InvalidArgumentException
     */
    protected function parseModel(string $model): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Queries';
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, Closure>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => fn (): string => text(
                label: 'What should the ' . strtolower($this->type) . ' be named?',
                placeholder: 'E.g. UserQueries',
                required: 'The name is required.',
                validate: function (string $value): ?string {
                    if (! Str::endsWith($value, 'Queries')) {
                        return 'The name must have suffix "Queries".';
                    }

                    return null;
                }
            ),
        ];
    }
}
