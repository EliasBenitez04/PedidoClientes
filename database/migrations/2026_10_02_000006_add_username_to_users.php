<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 80)->nullable()->unique()->after('name');
        });

        DB::statement('ALTER TABLE users ALTER COLUMN email DROP NOT NULL');

        $users = DB::table('users')->select('id', 'name', 'email')->orderBy('id')->get();

        foreach ($users as $user) {
            $base = '';

            if (!empty($user->email) && str_contains($user->email, '@')) {
                $base = explode('@', $user->email)[0];
            }

            if ($base === '') {
                $base = $user->name ?: 'USUARIO'.$user->id;
            }

            $base = strtoupper(Str::ascii($base));
            $base = preg_replace('/[^A-Z0-9_]/', '', str_replace(' ', '_', $base));
            $base = trim($base, '_');

            if ($base === '') {
                $base = 'USUARIO'.$user->id;
            }

            $candidate = substr($base, 0, 70);
            $suffix = 1;

            while (DB::table('users')->where('username', $candidate)->where('id', '<>', $user->id)->exists()) {
                $candidate = substr($base, 0, 65).'_'.$suffix;
                $suffix++;
            }

            DB::table('users')->where('id', $user->id)->update([
                'username' => $candidate,
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });

        DB::statement("UPDATE users SET email = CONCAT('usuario', id, '@pedidos.local') WHERE email IS NULL");
        DB::statement('ALTER TABLE users ALTER COLUMN email SET NOT NULL');
    }
};
