<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Place;
use App\Models\State;
use App\Services\PlaceService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

try {
    echo "1. Creating State...\n";
    $state = State::firstOrCreate(['name' => 'Cairo'], ['description' => 'Capital City']);
    
    echo "2. Creating Place...\n";
    $place = Place::create([
        'name' => 'Pyramids', 
        'address' => 'Giza', 
        'description' => 'Great Pyramids of Giza',
        'state_id' => $state->id
    ]);

    echo "3. Creating User...\n";
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    echo "4. Authenticating User...\n";
    Auth::login($user);

    echo "5. Calling PlaceService::checkIn...\n";
    $service = app(PlaceService::class);
    $visit = $service->checkIn($user, $place->id, 30.0, 31.0);

    echo "6. Success! Points awarded: {$visit->points_awarded}\n";
    echo "User XP: {$user->fresh()->exp}\n";

} catch (\Throwable $th) {
    echo "--- ERROR ---\n";
    echo $th->getMessage() . "\n";
    echo $th->getFile() . ":" . $th->getLine() . "\n";
    echo $th->getTraceAsString() . "\n";
}
