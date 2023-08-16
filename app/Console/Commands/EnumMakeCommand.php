<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Closure;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use function Laravel\Prompts\text;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:enum')]
class EnumMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Enum';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/enum.stub');
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
        return str_replace('{{ enum }}', Str::of($name)->classBasename()->value(), parent::buildClass($name));
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Enums';
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, Closure>
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => fn (): string => text(
                label: 'What should the ' . strtolower($this->type) . ' be named?',
                placeholder: 'E.g. StatusEnum',
                required: 'The name is required.',
                validate: function (string $value): ?string {
                    if (! Str::endsWith($value, 'Enum')) {
                        return 'The name must have suffix "Enum".';
                    }

                    return null;
                }
            ),
        ];
    }
}
