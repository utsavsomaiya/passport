<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class StaticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = text(
            label: 'What is your email?',
            placeholder: 'E.g. contact@artisanssolutions.com',
            required: 'Your email is required.',
            validate: function (string $value) {
                try {
                    Validator::validate(['email' => $value], ['email' => Rule::unique('users')]);
                } catch (Exception $exception) {
                    return $exception->getMessage();
                }
            },
        );

        $password = password(
            label: 'What is your password?',
            required: 'The password is required.',
            validate: function (string $value) {
                try {
                    Validator::validate(['password' => $value], ['password' => Password::defaults()]);
                } catch (Exception $exception) {
                    return $exception->getMessage();
                }
            }
        );

        $user = User::factory()->create([
            'first_name' => 'Product',
            'last_name' => 'Xperience Manager',
            'username' => 'product_xp_manager',
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        Role::factory()->named('Super Admin')->create([
            'description' => 'This role provides full access to the panel.',
        ]);

        $user->assignRoles(['Super Admin']);

        $this->command->info('A user has been successfully created with the <options=bold>Super Admin</> role.');
    }
}
