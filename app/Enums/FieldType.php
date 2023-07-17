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

    #[Description("For 'status' like fields: Yes/No or Enable/Disable")]
    case TOGGLE = 1;

    #[Description('For numbers with decimal point. e.g.: measurement = 5.5 feet')]
    case DECIMAL = 2;

    #[Description('For whole numbers. e.g.: quantity = 5')]
    case NUMBER = 3;

    #[Description('For regular text. e.g.: name = John Doe')]
    case TEXT = 4;

    #[Description('For dates. e.g.: a sale date = 12/07/2023')]
    case DATE = 5;

    #[Description('For date and time. e.g. 14 July 2023, 10:51 AM')]
    case DATETIME = 6;

    #[Description('For selecting one of the options. e.g. Gender = Male/Female')]
    case SELECT = 7;

    #[Description('For multiselect from the provided options. e.g. tags')]
    case LIST = 8;

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
     * @return array<int, string>
     */
    public function validation(mixed $from = null, mixed $to = null): array
    {
        $validations = collect([
            self::TOGGLE->value => ['boolean'],
            self::DECIMAL->value => $this->generateNumberValidation($from, $to),
            self::NUMBER->value => $this->generateNumberValidation($from, $to),
            self::TEXT->value => ['string'],
            self::DATE->value => $this->generateDateValidation($from, $to),
            self::DATETIME->value => $this->generateDateValidation($from, $to),
            // We allow to user the create a options..
            // If user can select the options which is not in the list it will create a new one...
            self::SELECT->value => ['string'],
            self::LIST->value => ['array'],
        ]);

        return $validations->get($this->value) ?? [];
    }

    /**
     * @return array<int, string>
     */
    private function generateDateValidation(mixed $from, mixed $to): array
    {
        $validations = ['date'];

        if ($from !== null) {
            if (strtotime($from) === false) {
                return $validations;
            }

            $validations[] = 'after:'.$from;
        }

        if ($to !== null) {
            if (strtotime($to) === false) {
                return $validations;
            }

            $validations[] = 'before:'.$to;
        }

        return $validations;
    }

    /**
     * @return array<int, string>
     */
    private function generateNumberValidation(mixed $from, mixed $to): array
    {
        if ($from === null && $to === null) {
            return [$this->value === self::NUMBER->value ? 'integer' : 'decimal'];
        }

        if ($from !== null) {
            if ($this->value === self::NUMBER->value) {
                return ['integer', 'min_digits:' . $from];
            }

            return ['decimal', 'min:'. $from];
        }

        if ($this->value === self::NUMBER->value) {
            return ['integer', 'max_digits:' . $to];
        }

        return ['decimal', 'max:'. $to];
    }

    public function fakerDefaultValue(): mixed
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
