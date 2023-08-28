<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\MetaProperties\Code;
use ArchTech\Enums\From;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;
use BackedEnum;
use Illuminate\Support\Str;

#[Meta(Code::class)]
enum Locale: int
{
    use Names;
    use Values;
    use Metadata;
    use From;

    #[Code('ab')]
    case ABKHAZIAN = 1;
    #[Code('af')]
    case AFRIKAANS = 2;
    #[Code('sq')]
    case ALBANIAN = 3;
    #[Code('hy')]
    case ARMENIAN = 4;
    #[Code('av')]
    case AVARIC = 5;
    #[Code('ae')]
    case AVESTAN = 6;
    #[Code('ay')]
    case AYMARA = 7;
    #[Code('az')]
    case AZERBAIJANI = 8;
    #[Code('eu')]
    case BASQUE = 9;
    #[Code('be')]
    case BELARUSIAN = 10;
    #[Code('bs')]
    case BOSNIAN = 11;
    #[Code('bg')]
    case BULGARIAN = 12;
    #[Code('ca')]
    case CATALAN = 13;
    #[Code('vl')]
    case VALENCIAN = 14;
    #[Code('co')]
    case CORSICAN = 15;
    #[Code('cs')]
    case CZECH = 16;
    #[Code('da')]
    case DANISH = 17;
    #[Code('nl')]
    case DUTCH = 18;
    #[Code('nl')]
    case FLEMISH = 19;
    #[Code('en')]
    case ENGLISH = 20;
    #[Code('et')]
    case ESTONIAN = 21;
    #[Code('fj')]
    case FIJIAN = 22;
    #[Code('fi')]
    case FINNISH = 23;
    #[Code('fr')]
    case FRENCH = 24;
    #[Code('de')]
    case GERMAN = 25;
    #[Code('gd')]
    case GAELIC = 26;
    #[Code('gd')]
    case SCOTTISH_GAELIC = 27;
    #[Code('ga')]
    case IRISH = 28;
    #[Code('el')]
    case GREEK = 29;
    #[Code('ht')]
    case HAITIAN = 30;
    #[Code('ht')]
    case HAITIAN_CREOLE = 31;
    #[Code('hr')]
    case CROATIAN = 32;
    #[Code('hu')]
    case HUNGARIAN = 33;
    #[Code('is')]
    case ICELANDIC = 34;
    #[Code('it')]
    case ITALIAN = 35;
    #[Code('lv')]
    case LATVIAN = 36;
    #[Code('lt')]
    case LITHUANIAN = 37;
    #[Code('lb')]
    case LUXEMBOURGISH = 38;
    #[Code('lb')]
    case LETZEBURGESCH = 39;
    #[Code('ms')]
    case MALAY = 40;
    #[Code('no')]
    case NORWEGIAN = 41;
    #[Code('fa')]
    case PERSIAN = 42;
    #[Code('pl')]
    case POLISH = 43;
    #[Code('pt')]
    case PORTUGUESE = 44;
    #[Code('ro')]
    case ROMANIAN = 45;
    #[Code('ro')]
    case MOLDAVIAN = 46;
    #[Code('ro')]
    case MOLDOVAN = 47;
    #[Code('ru')]
    case RUSSIAN = 48;
    #[Code('sk')]
    case SLOVAK = 49;
    #[Code('sl')]
    case SLOVENIAN = 50;
    #[Code('sm')]
    case SAMOAN = 51;
    #[Code('es')]
    case SPANISH = 52;
    #[Code('es')]
    case CASTILIAN = 53;
    #[Code('sc')]
    case SARDINIAN = 54;
    #[Code('sr')]
    case SERBIAN = 55;
    #[Code('sv')]
    case SWEDISH = 56;
    #[Code('uk')]
    case UKRAINIAN = 57;
    #[Code('cy')]
    case WELSH = 58;

    public static function listOfLocales()
    {
        return collect(self::cases())->map(fn (BackedEnum $case): array => [
            'id' => $case->value,
            'name' => Str::of($case->name)->replaceFirst('_', ' ')->title()->value(),
            'code' => $case->code(),
        ])->toArray();
    }
}
