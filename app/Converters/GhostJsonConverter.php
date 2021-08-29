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

            // Step 1: Remove some nodes
            $data = $this->removeUnnecessaryNodes($data);

            // Step 2: Convert user
            for ($i=0; $i < count($data['users']) ; $i++) {
                $data['users'][$i] = $this->convertUser($data['users'][$i]);
            }

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

    /**
     * removeUnnecessaryNodes
     *
     * @param array $data
     * @return array
     */
    public function removeUnnecessaryNodes(array $data): array
    {
        unset($data['app_fields']);
        unset($data['app_settings']);
        unset($data['apps']);
        unset($data['permissions']);
        unset($data['permissions_apps']);
        unset($data['permissions_roles']);
        unset($data['permissions_users']);
        return $data;
    }

    /**
     * convertUser
     *
     * @param array $user
     * @return array
     */
    public function convertUser(array $user): array
    {
        // cover -> cover_img
        $user = $this->changekey($user, 'cover', 'cover_img');
        // image -> profile_image
        $user = $this->changekey($user, 'image', 'profile_image');
        // uuid -> id
        $user = $this->changekey($user, 'uuid', 'id');
        // last_login -> last_seen
        $user = $this->changekey($user, 'last_login', 'last_seen');
        // language -> locale
        $user = $this->changekey($user, 'language', 'locale');
        // remove: created_by, updated_by
        unset($user['created_by']);
        unset($user['updated_by']);
        return $user;
    }

    /**
     * changekey of an array
     *
     * @param array $array
     * @param string $oldKey
     * @param string $newKey
     * @return array
     */
    public function changekey(array $array, string $oldKey, string $newKey): array
    {
        $array[$newKey] = $array[$oldKey];
        unset($array[$oldKey]);
        return $array;
    }
}
