<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Locale extends Model
{
    use Sushi;

    protected $rows = [
        ['name' => 'Abkhazian', 'code' => 'ab'],
        ['name' => 'Afrikaans', 'code' => 'af'],
        ['name' => 'Albanian', 'code' => 'sq'],
        ['name' => 'Armenian', 'code' => 'hy'],
        ['name' => 'Avaric', 'code' => 'av'],
        ['name' => 'Avestan', 'code' => 'ae'],
        ['name' => 'Aymara', 'code' => 'ay'],
        ['name' => 'Azerbaijani', 'code' => 'az'],
        ['name' => 'Basque', 'code' => 'eu'],
        ['name' => 'Belarusian', 'code' => 'be'],
        ['name' => 'Bosnian', 'code' => 'bs'],
        ['name' => 'Bulgarian', 'code' => 'bg'],
        ['name' => 'Catalan; Valencian', 'code' => 'ca'],
        ['name' => 'Corsican', 'code' => 'co'],
        ['name' => 'Czech', 'code' => 'cs'],
        ['name' => 'Danish', 'code' => 'da'],
        ['name' => 'Dutch; Flemish', 'code' => 'nl'],
        ['name' => 'English', 'code' => 'en'],
        ['name' => 'Estonian', 'code' => 'et'],
        ['name' => 'Fijian', 'code' => 'fj'],
        ['name' => 'Finnish', 'code' => 'fi'],
        ['name' => 'French', 'code' => 'fr'],
        ['name' => 'German', 'code' => 'de'],
        ['name' => 'Gaelic; Scottish Gaelic', 'code' => 'gd'],
        ['name' => 'Irish', 'code' => 'ga'],
        ['name' => 'Greek, Modern', 'code' => 'el'],
        ['name' => 'Haitian; Haitian Creole', 'code' => 'ht'],
        ['name' => 'Croatian', 'code' => 'hr'],
        ['name' => 'Hungarian', 'code' => 'hu'],
        ['name' => 'Icelandic', 'code' => 'is'],
        ['name' => 'Italian', 'code' => 'it'],
        ['name' => 'Latvian', 'code' => 'lv'],
        ['name' => 'Lithuanian', 'code' => 'lt'],
        ['name' => 'Luxembourgish; Letzeburgesch', 'code' => 'lb'],
        ['name' => 'Norwegian', 'code' => 'no'],
        ['name' => 'Persian', 'code' => 'fa'],
        ['name' => 'Polish', 'code' => 'pl'],
        ['name' => 'Portuguese', 'code' => 'pt'],
        ['name' => 'Romanian; Moldavian; Moldovan', 'code' => 'ro'],
        ['name' => 'Russian', 'code' => 'ru'],
        ['name' => 'Slovak', 'code' => 'sk'],
        ['name' => 'Slovenian', 'code' => 'sl'],
        ['name' => 'Samoan', 'code' => 'sm'],
        ['name' => 'Spanish; Castilian', 'code' => 'es'],
        ['name' => 'Sardinian', 'code' => 'sc'],
        ['name' => 'Serbian', 'code' => 'sr'],
        ['name' => 'Swedish', 'code' => 'sv'],
        ['name' => 'Ukrainian', 'code' => 'uk'],
        ['name' => 'Welsh', 'code' => 'cy'],
    ];
}
