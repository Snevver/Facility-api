<?php

namespace App\Controllers;

class FacilityController extends BaseController {

    /**
     * Helper function to fetch facilities from the database.
     * @param int|null $id Facility ID (optional).
     */
    private function fetchFacilities($id = null) {
        // I used AI to help me with this query since it was a bit complex and I wanted to make sure I got it right.
        $query = "SELECT facilities.*, 
                         locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number,
                         GROUP_CONCAT(tags.name) AS tags
                  FROM facilities
                  JOIN locations ON facilities.location_id = locations.id
                  LEFT JOIN facility_tags ON facilities.id = facility_tags.facility_id
                  LEFT JOIN tags ON facility_tags.tag_id = tags.id";
    
        if ($id !== null) {
            $query .= " WHERE facilities.id = :id GROUP BY facilities.id";
            $this->db->executeQuery($query, ['id' => $id]);
        } else {
            $query .= " GROUP BY facilities.id";
            $this->db->executeQuery($query);
        }
    
        return $this->db->getStatement()->fetchAll();
    }

    /**
     * Helper function to update facility tags in the database.
     * @param int $facility_id Facility ID.
     * @param array $tags_id Array of tag IDs.
     */
    private function updateTags($facility_id, $tags_id) {
        $query = "DELETE FROM facility_tags WHERE facility_id = :facility_id";
        $this->db->executeQuery($query, ['facility_id' => $facility_id]);
    
        if (!empty($tags_id) && is_array($tags_id)) {
            foreach ($tags_id as $tag_id) {
                if (!is_numeric($tag_id)) {
                    continue;
                }

                // Check if the tag exists
                $query = "SELECT COUNT(*) FROM tags WHERE id = :tag_id";
                $this->db->executeQuery($query, ['tag_id' => $tag_id]);
                $count = $this->db->getStatement()->fetchColumn();
                if ($count == 0) {
                    (new \App\Plugins\Http\Response\BadRequest(['message' => 'Tag ID ' . $tag_id . ' does not exist']))->send();
                    return;
                }
    
                $query = "INSERT INTO facility_tags (facility_id, tag_id) VALUES (:facility_id, :tag_id)";
                $this->db->executeQuery($query, [
                    'facility_id' => $facility_id,
                    'tag_id' => $tag_id,
                ]);
            }
        }
    }

    /**
     * Get a specific facility by ID.
     * @param int $id Facility ID.
     * @return void
     */
    public function getAllFacilities() {
        try {
            $facilities = $this->fetchFacilities();

            if (!empty($facilities)) {
                (new \App\Plugins\Http\Response\Ok($facilities))->send();
            } else {
                (new \App\Plugins\Http\Response\NotFound(['message' => 'No facilities found']))->send();
            }
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
     * Get a specific facility by ID.
     * @param int $id Facility ID.
     * @return void
     */
    public function getFacility($id) {
        try {
            $facility = $this->fetchFacilities($id);
    
            if (!empty($facility)) {
                (new \App\Plugins\Http\Response\Ok($facility[0]))->send();
            } else {
                (new \App\Plugins\Http\Response\NotFound(['message' => 'Facility not found']))->send();
            }
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
     * Create a new facility.
     * Fill in the new data in the request body in Postman:)
     * Example body: 
     * {
     *    "name": "New Facility Name", 
     *    "location_id": 2,
     *    "tags_id": [1, 2]
     * }
     * I feel like this method could be improbved a bit, but I wanted to keep it simple and easy to understand.
     * @param int $id Facility ID.
     * @return void
     */
    public function createFacility() {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
    
            if (empty($data['name']) || empty($data['location_id'])) {
                (new \App\Plugins\Http\Response\BadRequest(['message' => 'Name and location_id are required fields']))->send();
                return;
            }
            
            // Validate location_id
            $query = "SELECT COUNT(*) FROM locations WHERE id = :location_id";
            $this->db->executeQuery($query, ['location_id' => $data['location_id']]);
            $locationExists = $this->db->getStatement()->fetchColumn();
            if ($locationExists == 0) {
                (new \App\Plugins\Http\Response\BadRequest(['message' => 'Location ID ' . $data['location_id'] . ' does not exist']))->send();
                return;
            }

            // Validate tags_id (If provided)
            if (!empty($data['tags_id']) && is_array($data['tags_id'])) {
                foreach ($data['tags_id'] as $tag_id) {
                    if (!is_numeric($tag_id)) {
                        (new \App\Plugins\Http\Response\BadRequest(['message' => 'Invalid tag ID: ' . $tag_id]))->send();
                        return;
                    }
    
                    // Check if the tag exists in the database
                    $query = "SELECT COUNT(*) FROM tags WHERE id = :tag_id";
                    $this->db->executeQuery($query, ['tag_id' => $tag_id]);
                    $count = $this->db->getStatement()->fetchColumn();
                    if ($count == 0) {
                        (new \App\Plugins\Http\Response\BadRequest(['message' => 'Tag ID ' . $tag_id . ' does not exist']))->send();
                        return;
                    }
                }
            }
    
            // Insert the facility into the database
            $query = "INSERT INTO facilities (name, location_id, creation_date) VALUES (:name, :location_id, NOW())";
            $this->db->executeQuery($query, [
                'name' => $data['name'],
                'location_id' => $data['location_id'],
            ]);
    
            $facility_id = $this->db->getLastInsertedId();
            $this->updateTags($facility_id, $data['tags_id'] ?? []);
    
            (new \App\Plugins\Http\Response\Created(['message' => 'Facility created successfully']))->send();
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
     * Update a facility by ID.
     * @param int $id Facility ID.
     * @return void
     */
    public function editFacility($id) {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
    
            if (empty($data['name']) || empty($data['location_id'])) {
                (new \App\Plugins\Http\Response\BadRequest(['message' => 'Name and location_id are required fields']))->send();
                return;
            }
    
            $query = "UPDATE facilities SET name = :name, location_id = :location_id WHERE id = :id";
            $this->db->executeQuery($query, [
                'name' => $data['name'],
                'location_id' => $data['location_id'],
                'id' => $id,
            ]);
    
            if ($this->db->getStatement()->rowCount() > 0) {
                $this->updateTags($id, $data['tags_id'] ?? []);
    
                (new \App\Plugins\Http\Response\Ok(['message' => 'Facility updated successfully']))->send();
            } else {
                (new \App\Plugins\Http\Response\NotFound(['message' => 'Facility not found']))->send();
            }
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
     * Delete a facility by ID.
     * @param int $id Facility ID.
     * @return void
     */
    public function deleteFacility($id) {
        try {
            $query = "DELETE FROM facility_tags WHERE facility_id = :facility_id";
            $this->db->executeQuery($query, ['facility_id' => $id]);
    
            $query = "DELETE FROM facilities WHERE id = :id";
            $this->db->executeQuery($query, ['id' => $id]);
    
            if ($this->db->getStatement()->rowCount() > 0) {
                (new \App\Plugins\Http\Response\Ok(['message' => 'Facility deleted successfully']))->send();
            } else {
                (new \App\Plugins\Http\Response\NotFound(['message' => 'Facility not found']))->send();
            }
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }
}