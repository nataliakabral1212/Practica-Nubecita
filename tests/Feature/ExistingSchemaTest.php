<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('migrations preserve existing data and fill in missing schema objects', function () {
    $user = User::factory()->create();
    DB::table('users')->where('id', $user->id)->update(['two_factor_secret' => 'existing-value']);
    DB::table('cache')->insert(['key' => 'existing-entry', 'value' => 'kept', 'expiration' => 1]);
    Schema::drop('sessions');
    Schema::drop('failed_jobs');
    Schema::dropColumns('users', ['two_factor_recovery_codes', 'two_factor_confirmed_at']);

    $migrations = glob(database_path('migrations/*.php'));

    foreach ($migrations as $path) {
        (require $path)->up();
    }

    expect(Schema::hasTable('sessions'))->toBeTrue();
    expect(Schema::hasTable('failed_jobs'))->toBeTrue();
    expect(Schema::hasColumns('users', ['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at']))->toBeTrue();
    $this->assertDatabaseHas('users', ['id' => $user->id, 'two_factor_secret' => 'existing-value']);
    $this->assertDatabaseHas('cache', ['key' => 'existing-entry', 'value' => 'kept']);
});
