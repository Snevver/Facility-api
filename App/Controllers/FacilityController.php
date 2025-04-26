<?php

namespace App\Controllers;

class FacilityController extends BaseController {
    /**
     * Get a specific facility by ID.
     * @param int $id Facility ID.
     * @return void
     */
    public function getAllFacilities() {
        try {
            $query = "SELECT facilities.*, locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number
                      FROM facilities
                      JOIN locations ON facilities.location_id = locations.id";
            $result = $this->db->executeQuery($query);
    
            if ($result) {
                $facilities = $this->db->getStatement()->fetchAll();
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
            $query = "SELECT facilities.*, locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number
                      FROM facilities
                      JOIN locations ON facilities.location_id = locations.id
                      WHERE facilities.id = :id";
            $this->db->executeQuery($query, ['id' => $id]);
    
            if ($this->db->getStatement()->rowCount() > 0) {
                $facility = $this->db->getStatement()->fetch();
                (new \App\Plugins\Http\Response\Ok($facility))->send();
            } else {
                (new \App\Plugins\Http\Response\NotFound(['message' => 'Facility not found']))->send();
            }
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
     * Create a new facility.
     * @return void
     */
    public function createFacility() {
        try {
            // Fill in the new data in the request body in Postman:)
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
    
            if (empty($data['name']) || empty($data['location_id'])) {
                (new \App\Plugins\Http\Response\BadRequest(['message' => 'Name and location_id are required fields']))->send();
                return;
            }
            
            // The creationm_date is set to the current date and time in the database, you dont need to set it in the request body.
            $query = "INSERT INTO facilities (name, location_id, creation_date) VALUES (:name, :location_id, NOW())";
            $this->db->executeQuery($query, [
                'name' => $data['name'],
                'location_id' => $data['location_id'],
            ]);
    
            (new \App\Plugins\Http\Response\Created(['message' => 'Facility created successfully']))->send();
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }
}