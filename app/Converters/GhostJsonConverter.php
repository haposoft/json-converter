<?php

namespace App\Converters;

use Illuminate\Support\Facades\Log;

class GhostJsonConverter
{
    const INPUT_FILE = 'storage/app/public/hapolog.ghost.2021-08-29.json';
    const OUTPUT_FILE = 'storage/app/public/hapolog.ghost.4.json';

    public function convert()
    {
        try {
            // Read File
            $jsonString = file_get_contents(base_path(self::INPUT_FILE));

            $db = json_decode($jsonString, true);

            $data = $db['db'][0]['data'];

            // TODO: Update then import test in ghost

            // Remove some nodes:
            unset($data['app_fields']);
            unset($data['app_settings']);
            unset($data['apps']);
            unset($data['permissions']);
            unset($data['permissions_apps']);
            unset($data['permissions_roles']);
            unset($data['permissions_users']);

            // TODO:


            // Affter convert, Re-append data back to db
            $db['db'][0]['data'] = $data;
            // Write File
            $newJsonString = json_encode($db, JSON_PRETTY_PRINT);
            // test
            // dd(__($newJsonString));
            file_put_contents(base_path(self::OUTPUT_FILE), $newJsonString);
        } catch (\Throwable $th) {
            echo $th->getMessage();
            Log::error($th->getMessage());
        }
    }
}
