<?php

namespace App\Services;

class TagService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Update facility tags by tag names. Creates tags if they do not exist.
     * @param int $facility_id Facility ID.
     * @param array $tagNames Array of tag names.
     */
    public function updateTags($facility_id, $tagNames) {
        // Remove all existing tags for this facility
        $query = "DELETE FROM facility_tags WHERE facility_id = :facility_id";
        $this->db->executeQuery($query, ['facility_id' => $facility_id]);

        if (!empty($tagNames) && is_array($tagNames)) {
            foreach ($tagNames as $tagName) {
                $tagName = trim($tagName);
                if ($tagName === '') continue;
                // Check if tag exists
                $query = "SELECT id FROM tags WHERE name = :name";
                $this->db->executeQuery($query, ['name' => $tagName]);
                $tagId = $this->db->getStatement()->fetchColumn();
                if (!$tagId) {
                    // Create tag if it doesn't exist
                    $query = "INSERT INTO tags (name) VALUES (:name)";
                    $this->db->executeQuery($query, ['name' => $tagName]);
                    $tagId = $this->db->getLastInsertedId();
                }
                // Associate tag with facility
                $query = "INSERT INTO facility_tags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";
                $this->db->executeQuery($query, [
                    'facility_id' => $facility_id,
                    'tag_id' => $tagId,
                ]);
            }
        }
    }
}