<?php

namespace App\Services;

class ValidationService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Validate the input data for creating or updating a facility.
     * @param array $tags Array of tag names.
     */
    public function validateTags($tags) {
        foreach ($tags as $tag) {
            if (!is_string($tag) || trim($tag) === '') {
                throw new \Exception('Invalid tag name: ' . print_r($tag, true));
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

    public function validateFacilityId($id) {
        if (empty($id) || !is_numeric($id)) {
            (new \App\Plugins\Http\Response\BadRequest(['message' => 'Invalid facility ID']))->send();
            return;
        }
    }
}