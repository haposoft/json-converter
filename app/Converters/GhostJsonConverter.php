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
            // update version
            $db['db'][0]['meta']['version'] = "4.12.1";

            $data = $db['db'][0]['data'];

            // TODO: Update then import test in ghost

            // Step 1: Remove some nodes
            $data = $this->removeUnnecessaryNodes($data);

            // Step 2: Convert user
            // for ($i = 0; $i < count($data['users']); $i++) {
            //     $data['users'][$i] = $this->convertUser($data['users'][$i]);
            // }
            // Step 3: Convert post
            for ($i = 0; $i < count($data['posts']); $i++) {
                $postResult = $this->convertPost($data['posts'][$i]);
                $data['posts'][$i] = $postResult['post'];
                $data['posts_authors'][] = $postResult['post_author'];
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

        // test
        unset($data['roles']);
        unset($data['roles_users']);
        unset($data['settings']);
        unset($data['tags']);
        unset($data['users']);
        unset($data['posts_tags']);

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
        // last_login -> last_seen
        $user = $this->changekey($user, 'last_login', 'last_seen');
        // language -> locale
        $user = $this->changekey($user, 'language', 'locale');
        // remove: created_by, updated_by
        unset($user['created_by']);
        unset($user['updated_by']);
        return $user;
    }

    public function convertPost(array $post): array
    {
        // image -> feature_image
        $post = $this->changekey($post, 'image', 'feature_image');
        // markdown -> plaintext
        $post = $this->changekey($post, 'markdown', 'plaintext');
        // language -> locale
        $post = $this->changekey($post, 'language', 'locale');

        // set default author id (test)
        $post['author_id'] = '1';
        $post['id'] = uniqid();
        $post['comment_id'] =  $post['id'];

        $post['canonical_url'] = null;
        $post['codeinjection_foot'] = null;
        $post['codeinjection_head'] = null;

        $post['custom_excerpt'] = null;
        $post['custom_template'] = null;
        $post['email_recipient_filter'] = "none";

        $post['type'] = "post";

        // remove: created_by, updated_by ...
        unset($post['created_by']);
        unset($post['updated_by']);
        unset($post['amp']);
        unset($post['meta_description']);
        unset($post['meta_title']);
        unset($post['page']);
        unset($post['published_by']);

        // Fake post author
        $post_author = [
            'id' => uniqid(),
            'post_id' => $post['id'],
            'author_id' => '1',
            'sort_order' => 0
        ];

        return [
            'post' => $post,
            'post_author' => $post_author
        ];
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
