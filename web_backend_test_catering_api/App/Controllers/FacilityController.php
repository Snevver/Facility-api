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
    * @param int $id Facility ID.
    */
    public function getFacility($id) {
        try {
            $facility = $this->facilityService->fetchFacilities($id);

            if (!empty($facility)) {
                // Convert tags from comma-separated string to array
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
            $this->validationService->validateLocation($data['location_id']);

            // Validate tags_id (if provided)
            if (!empty($data['tags_id']) && is_array($data['tags_id'])) {
                $this->validationService->validateTags($data['tags_id']);
            }

            // Create the facility
            $facility_id = $this->facilityService->createFacility($data['name'], $data['location_id']);
            $this->tagService->updateTags($facility_id, $data['tags_id'] ?? []);

            (new \App\Plugins\Http\Response\Created(['message' => 'Facility created successfully']))->send();
        } catch (\Exception $e) {
            (new \App\Plugins\Http\Response\InternalServerError(['message' => 'An error occurred: ' . $e->getMessage()]))->send();
        }
    }

    /**
    * Update a facility by ID.
    * @param int $id Facility ID.
    */
    public function editFacility($id) {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (empty($data['name']) || empty($data['location_id'])) {
                (new \App\Plugins\Http\Response\BadRequest(['message' => 'Name and location_id are required fields']))->send();
                return;
            }

            // Validate tags_id (if provided)
            if (!empty($data['tags_id']) && is_array($data['tags_id'])) {
                $this->validationService->validateTags($data['tags_id']);
            }

            // Update the facility
            $updated = $this->facilityService->updateFacility($id, $data['name'], $data['location_id']);
            if ($updated) {
                $this->tagService->updateTags($id, $data['tags_id'] ?? []);
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
    */
    public function deleteFacility($id) {
        try {
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