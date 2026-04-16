<?php

// use App\Models\Bank;
// use Illuminate\Console\Command;
// use App\Models\Place;
// use App\Models\Hotel;
// use App\Models\Restaurant;

// class ProcessPlacesCSVs extends Command
// {
//   protected $signature = 'csv:process-places';

//   protected $description = 'Process places CSV file and save to database';

//   public function handle()
//   {
//     // Read the CSV file
//     $csvFile = storage_path('app/all-Banks.csv');
//     $csvData = array_map('str_getcsv', file($csvFile));

//     // Remove header row
//     $header = array_shift($csvData);

//     // Process each row
//     foreach ($csvData as $row) {
//       $state_id = $row[0];
//       $name = $row[2];
//       $location = $row[4];
//       $rate = $row[3];
//       $address = $row[1];
//       // switch ($state_id) {
//       //   case 1:
//       //     break;
//       //   case 2:
//       //     $address = 'Luxor';
//       //     break;
//       //   case 3:
//       //     $address = 'Cairo';
//       //     break;
//       //   case 4:
//       //     $address = 'Aswan';
//       //     break;
//       //   case 5:
//       //     $address = 'Red Sea';
//       //     break;
//       //   default:
//       //     $address = 'Unknown'; 
//       //     break;
//       // }
//       // $row[1]
//       // $images = array_slice($row, 4); // Get images as an array

//       // Create place record
//       $hotel = Bank::create([
//         'state_id' => $state_id,
//         'name' => $name,
//         'location' => $location,
//         'address' => $address,
//         'rate' => $rate,
//       ]);

//       // Save images for the place
//       // foreach ($images as $image) {
//       //   $hotel->images()->create(['data' => $image]);
//       // }
//     }

//     $this->info('Places processed and saved successfully.');
//   }
// }
