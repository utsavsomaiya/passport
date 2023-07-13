<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\MetaProperties\Description;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Values;
use Illuminate\Support\Str;

#[Meta(Description::class)]
enum FieldType: int
{
    use Values;
    use Metadata;

    #[Description('A button that a user clicks to set an option. e.g.: gender = male or female')]
    case TOGGLE = 1;

    #[Description('A text box only allow the decimal numbers. e.g.: measurement = 5.5 feet')]
    case DECIMAL = 2;

    #[Description('A text box only allow the integer numbers. e.g.: quantity = 5')]
    case NUMBER = 3;

    #[Description('A text box only allow the string values. e.g.: name = John Doe')]
    case TEXT = 4;

    #[Description('A text box only select the dates. e.g.: a sale happened_at = 12/07/2023, 09:35 PM')]
    case DATE = 5;

    #[Description('A selection box select the single value. e.g. city = San Francisco')]
    case SELECT = 6;

    #[Description('A selection box which allows multiselect. e.g. tagging')]
    case LIST = 7;

    /**
     * @return array<string, string>
     */
    public static function fetchTheValueForUI(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case): array => [
            Str::title($case->name) => $case->value,
        ])->toArray();
    }

    /**
     * @return array<int, string>|null
     */
    public function validation(mixed $from = null, mixed $to = null): ?array
    {
        $validations = collect([
            self::TOGGLE->value => ['boolean'],
            self::DECIMAL->value => $this->generateNumberValidation($from, $to),
            self::NUMBER->value => $this->generateNumberValidation($from, $to),
            self::TEXT->value => ['string'],
            self::DATE->value => ['date'],
            self::SELECT->value => ['string'],
            self::LIST->value => ['array'],
        ]);

        return $validations->get($this->value);
    }

    /**
     * @return array<int, string>| null
     */
    private function generateNumberValidation(mixed $from, mixed $to): ?array
    {
        if ($from === null) {
            return [$this->value === self::NUMBER->value ? 'integer' : 'decimal'];
        }

        if ($to === null) {
            return [$this->value === self::NUMBER->value ? 'integer' : 'decimal'];
        }

        if ($this->value === self::NUMBER->value) {
            return ['integer', 'digits_between:' . $from . ',' . $to];
        }

        if ($this->value === self::DECIMAL->value) {
            return ['decimal', 'min:'. $from, 'max:' . $to];
        }

        return null;
    }

    public function defaultValue(): mixed
    {
        $defaultValues = collect([
            self::TOGGLE->value => fake()->boolean(),
            self::DECIMAL->value => 0.00,
            self::NUMBER->value => 0,
            self::TEXT->value => fake()->word(),
            self::DATE->value => fake()->date(),
            self::LIST->value => fake()->randomElements(fake()->words(6), 2),
        ]);

        return $defaultValues->get($this->value);
    }

    public function resourceName(): string
    {
        return Str::title($this->name);
    }

    /**
     * @return array<int, self>
     */
    public static function selections(): array
    {
        return [self::SELECT, self::LIST];
    }

    public static function getValidationValues(): string
    {
        return collect(self::values())->implode(',');
    }
}
