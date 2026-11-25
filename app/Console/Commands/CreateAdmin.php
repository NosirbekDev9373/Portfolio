<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {email}';

    protected $description = 'Create or update an admin user without deleting other users';

    public function handle(): int
    {
        $email = (string) $this->argument('email');

        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = $this->secret('Password (min. 12 characters)');

        if (! is_string($password) || mb_strlen($password) < 12) {
            $this->error('Password must be at least 12 characters.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name ??= 'Admin';
        $user->password = $password;
        $user->save();

        $this->info("Admin user [{$email}] is ready.");

        return self::SUCCESS;
    }
}
