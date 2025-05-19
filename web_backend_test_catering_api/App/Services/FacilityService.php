<?php

namespace App\Services;

class FacilityService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
    * Helper function to fetch facilities from the database. This function can be used to fetch all facilities or a specific facility by ID :)
    * @param int|null $id Facility ID (optional).
    */
    public function fetchFacilities($id = null) {
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
     * Helper function to create a new facility in the database.
     * @param string $name Facility name.
     * @param int $location_id Location ID.
     */
    public function createFacility($name, $location_id) {
        $query = "INSERT INTO facilities (name, location_id, creation_date) VALUES (:name, :location_id, NOW())";
        $this->db->executeQuery($query, [
            'name' => $name,
            'location_id' => $location_id,
        ]);

        return $this->db->getLastInsertedId();
    }

    /**
     * Helper function to update an existing facility in the database.
     * @param int $id Facility ID.
     * @param string $name Facility name.
     * @param int $location_id Location ID.
     */
    public function updateFacility($id, $name, $location_id) {
        $query = "UPDATE facilities SET name = :name, location_id = :location_id WHERE id = :id";
        $this->db->executeQuery($query, [
            'name' => $name,
            'location_id' => $location_id,
            'id' => $id,
        ]);

        return $this->db->getStatement()->rowCount() > 0;
    }

    /**
     * Helper function to delete a facility from the database.
     * @param int $id Facility ID.
     */
    public function deleteFacility($id) {
        // Delte the tags first to avoid foreign key constraint errors
        $query = "DELETE FROM facility_tags WHERE facility_id = :id";
        $this->db->executeQuery($query, ['id' => $id]);

        // Then delete the facility
        $query = "DELETE FROM facilities WHERE id = :id";
        $this->db->executeQuery($query, ['id' => $id]);

        return $this->db->getStatement()->rowCount() > 0;
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

    /**
     * Search for facilities by facility name, tags, location, or any combination of those.
     * @param array $filters
     * @return array
     */
    public function searchFacilities($filters) {
        $query = "SELECT facilities.*, 
                        locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number,
                        GROUP_CONCAT(tags.name) AS tags
                FROM facilities
                JOIN locations ON facilities.location_id = locations.id
                LEFT JOIN facility_tags ON facilities.id = facility_tags.facility_id
                LEFT JOIN tags ON facility_tags.tag_id = tags.id";

        $conditions = [];
        $parameters = [];

        if (!empty($filters['city'])) {
            $conditions[] = "locations.city = :city";
            $parameters['city'] = $filters['city'];
        }
        if (!empty($filters['name'])) {
            $conditions[] = "facilities.name LIKE :name";
            $parameters['name'] = '%' . $filters['name'] . '%';
        }
        if (!empty($filters['tag'])) {
            $conditions[] = "tags.name LIKE :tag";
            $parameters['tag'] = '%' . $filters['tag'] . '%';
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " GROUP BY facilities.id";

        $this->db->executeQuery($query, $parameters);

        return $this->db->getStatement()->fetchAll();
    }
}