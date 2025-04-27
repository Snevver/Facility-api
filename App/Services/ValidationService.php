<?php

namespace App\Services;

class ValidationService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Validate the input data for creating or updating a facility.
     * @param array $tags_id Array of tag IDs.
     */
    public function validateTags($tags_id) {
        foreach ($tags_id as $tag_id) {
            if (!is_numeric($tag_id)) {
                throw new \Exception('Invalid tag ID: ' . $tag_id);
            }
            $query = "SELECT COUNT(*) FROM tags WHERE id = :tag_id";
            $this->db->executeQuery($query, ['tag_id' => $tag_id]);
            if ($this->db->getStatement()->fetchColumn() == 0) {
                throw new \Exception('Tag ID ' . $tag_id . ' does not exist');
            }
        }
    }

    /**
     * Validate the input data for creating or updating a facility.
     * @param int $location_id Location ID.
     */
    public function validateLocation($location_id) {
        $query = "SELECT COUNT(*) FROM locations WHERE id = :location_id";
        $this->db->executeQuery($query, ['location_id' => $location_id]);
        if ($this->db->getStatement()->fetchColumn() == 0) {
            throw new \Exception('Location ID ' . $location_id . ' does not exist');
        }
    }
}