<?php

namespace App\Services;

class TagService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Helper function to update facility tags in the database.
     * @param int $facility_id Facility ID.
     * @param array $tags_id Array of tag IDs.
     */
    public function updateTags($facility_id, $tags_id) {
        $query = "DELETE FROM facility_tags WHERE facility_id = :facility_id";
        $this->db->executeQuery($query, ['facility_id' => $facility_id]);

        if (!empty($tags_id) && is_array($tags_id)) {
            foreach ($tags_id as $tag_id) {
                $query = "INSERT INTO facility_tags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";
                $this->db->executeQuery($query, [
                    'facility_id' => $facility_id,
                    'tag_id' => $tag_id,
                ]);
            }
        }
    }
}