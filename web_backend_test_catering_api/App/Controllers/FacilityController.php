<?php

namespace App\Controllers;

use App\Services\FacilityService;
use App\Services\ValidationService;
use App\Services\TagService;

class FacilityController extends BaseController {
    private $facilityService;
    private $validationService;
    private $tagService;

    public function __construct() {
        $db = $this->db;
        
        $this->facilityService = new FacilityService($db);
        $this->validationService = new ValidationService($db);
        $this->tagService = new TagService($db);
    }

    /**
    * Get all facilities.
    *
    * @route GET /facilities
    * @return 200 OK - List of all facilities
    * @response
    * [
    *   {
    *     "id": 1,
    *     "name": "Facility Name",
    *     "location_id": 2,
    *     "tags": ["tag1", "tag2"]
    *   },
    *   ...
    * ]
    */
    public function getAllFacilities() {
        try {
            $facilities = $this->facilityService->fetchFacilities();

            // Convert tags from comma-separated string to array
            foreach ($facilities as &$facility) {
                if (isset($facility['tags'])) {
                    $facility['tags'] = $facility['tags'] !== null && $facility['tags'] !== ''
                        ? explode(',', $facility['tags'])
                        : [];
                }
            }

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
    *
    * @route GET /facility/{id}
    * @param int $id Facility ID (required)
    * @return 200 OK - Facility object
    * @response
    * {
    *   "id": 1,
    *   "name": "Facility Name",
    *   "location_id": 2,
    *   "tags": ["tag1", "tag2"]
    * }
    */
    public function getFacility($id) {
        try {
            $this->validationService->validateFacilityId($id);

            $facility = $this->facilityService->fetchFacilities($id);

            if (!empty($facility)) {
                if (isset($facility[0]['tags'])) {
                    $facility[0]['tags'] = $facility[0]['tags'] !== null && $facility[0]['tags'] !== ''
                        ? explode(',', $facility[0]['tags'])
                        : [];
                }
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
    *
    * @route POST /create
    * @bodyParam name string required The name of the facility.
    * @bodyParam location_id int required The location ID.
    * @bodyParam tags string[] optional Array of tag names.
    * @example {
    *   "name": "New Facility",
    *   "location_id": 1,
    *   "tags": ["Food", "Catering"]
    * }
    * @return 201 Created - Facility created successfully
    * @response
    * {
    *   "message": "Facility created successfully"
    * }
    */
    public function createFacility() {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (empty($data['name']) || empty($data['location_id'])) {
                (new \App\Plugins\Http\Response\BadRequest(['message' => 'Name and location_id are required fields']))->send();
                return;
            }

            $this->validationService->validateLocation($data['location_id']);

            if (!empty($data['tags']) && is_array($data['tags'])) {
                $this->validationService->validateTags($data['tags']);
            }

            $facility_id = $this->facilityService->createFacility($data['name'], $data['location_id']);
            $this->tagService->updateTags($facility_id, $data['tags'] ?? []);

            (new \App\Plugins\Http\Response\Created(['message' => 'Facility created successfully']))->send();
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
    * Update a facility by ID.
    *
    * @route PUT /edit/{id}
    * @param int $id Facility ID (required)
    * @bodyParam name string required The name of the facility.
    * @bodyParam location_id int required The location ID.
    * @bodyParam tags string[] optional Array of tag names.
    * @example {
    *   "name": "Updated Facility",
    *   "location_id": 2,
    *   "tags": ["Drinks", "Events"]
    * }
    * @return 200 OK - Facility updated successfully
    * @response
    * {
    *   "message": "Facility updated successfully"
    * }
    */
    public function editFacility($id) {
        try {
            $this->validationService->validateFacilityId($id);

            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (empty($data['name']) || empty($data['location_id'])) {
                (new \App\Plugins\Http\Response\BadRequest(['message' => 'Name and location_id are required fields']))->send();
                return;
            }

            // Validate tags (if provided)
            if (!empty($data['tags']) && is_array($data['tags'])) {
                $this->validationService->validateTags($data['tags']);
            }

            // Update the facility
            $updated = $this->facilityService->updateFacility($id, $data['name'], $data['location_id']);
            if ($updated) {
                $this->tagService->updateTags($id, $data['tags'] ?? []);
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
    *
    * @route DELETE /delete/{id}
    * @param int $id Facility ID (required)
    * @return 200 OK - Facility deleted successfully
    * @response
    * {
    *   "message": "Facility deleted successfully"
    * }
    */
    public function deleteFacility($id) {
        try {
            $this->validationService->validateFacilityId($id);

            $deleted = $this->facilityService->deleteFacility($id);

            if ($deleted) {
                (new \App\Plugins\Http\Response\Ok(['message' => 'Facility deleted successfully']))->send();
            } else {
                (new \App\Plugins\Http\Response\NotFound(['message' => 'Facility not found']))->send();
            }
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
    * Search for facilities.
    *
    * @route GET /search
    * @queryParam name string optional Facility name to search for.
    * @queryParam tag string optional Tag name to filter by.
    * @queryParam location_id int optional Location ID to filter by.
    * @return 200 OK - List of matching facilities
    * @response
    * [
    *   {
    *     "id": 1,
    *     "name": "Facility Name",
    *     "location_id": 2,
    *     "tags": ["tag1", "tag2"]
    *   },
    * ]
    */
    public function searchFacility() {
        try {
            $facilities = $this->facilityService->searchFacilities($_GET);

            foreach ($facilities as &$facility) {
                if (isset($facility['tags'])) {
                    $facility['tags'] = $facility['tags'] !== null && $facility['tags'] !== ''
                        ? explode(',', $facility['tags'])
                        : [];
                }
            }

            if (!empty($facilities)) {
                (new \App\Plugins\Http\Response\Ok($facilities))->send();
            } else {
                (new \App\Plugins\Http\Response\NotFound(['message' => 'No facilities found']))->send();
            }
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }
}