<?php

namespace MattYeend\DynamicEnumGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateEnums extends Command
{
    protected $signature = 'enum:generate {--table=} {--column=} {--path=app/Enums}';
    protected $description = 'Generate PHP enums from database columns automatically';

    public function handle()
    {
        $table = $this->option('table');
        $column = $this->option('column');
        $path = base_path($this->option('path'));

        if (!$table || !$column) {
            $this->error('You must specify both --table and --column options.');
            return;
        }

        $values = DB::table($table)
            ->distinct()
            ->pluck($column)
            ->filter()
            ->toArray();

        if (empty($values)) {
            $this->warn('No values found for the column.');
            return;
        }

        $enumName = Str::studly($column);
        $enumFile = $path . '/' . $enumName . '.php';

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $enumContent = $this->generateEnumContent($enumName, $values);
        File::put($enumFile, $enumContent);

        $this->info("Enum {$enumName} generated at {$enumFile}");
    }

    protected function generateEnumContent(string $enumName, array $values): string
    {
        $cases = '';
        foreach ($values as $value) {
            $caseName = strtoupper(preg_replace('/\s+/', '_', $value));
            $cases .= "    case {$caseName} = '{$value}';\n";
        }

        return <<<PHP
<?php

namespace App\Enums;

enum {$enumName}: string
{
{$cases}}
PHP;
    }
}
