<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'make:builder')]
class BuilderMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:builder {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new builder';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Builder';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/builder.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        if (Str::endsWith(class_basename($name), 'Builder')) {
            return parent::getPath($name);
        }

        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return app_path().'/'.str_replace('\\', '/', $name).'Builder.php';
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        if (Str::endsWith(class_basename($name), 'Builder')) {
            return str_replace('{{ class }}', Str::of($name)->classBasename()->value(), $stub);
        }

        return str_replace('{{ class }}', Str::of($name)->classBasename()->value() . 'Builder', $stub);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
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

        if (Str::endsWith($name, 'Builder')) {
            $modelClass = $this->parseModel(Str::before($name, 'Builder'));
        } else {
            $modelClass = $this->parseModel($name);
        }

        if (
            ! class_exists($modelClass) &&
            $this->components->confirm(sprintf('A %s model does not exist. Do you want to generate it?', $modelClass), true)
        ) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        return [
            '{{ model }}' => class_basename($modelClass),
            '{{ namespacedModel }}' => $modelClass,
        ];
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Builders';
    }

    /**
     * Get the console command arguments.
     *
     * @return array<int, array<int, int|string>>
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the enum'],
        ];
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }
}
