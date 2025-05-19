<?php

/** @var Bramus\Router\Router $router */

// Define routes here

/**
 * Route for testing
 * @method GET
 * @uri /test
 * @controller App\Controllers\IndexController
 * @action test
 * @description This route is used for testing purposes.
 */
$router->get('/test', App\Controllers\IndexController::class . '@test');

/**
 * Route for getting all facilities
 * @method GET
 * @uri /facilities
 * @controller App\Controllers\FacilityController
 * @action getAllFacilities
 * @description This route fetches all facilities.
 */
$router->get('/facilities', App\Controllers\FacilityController::class . '@getAllFacilities');

/**
 * Route for getting a specific facility by ID
 * @method GET
 * @uri /facility/{{id}}
 * @controller App\Controllers\FacilityController
 * @action getFacility
 * @description This route fetches a specific facility by its ID.
 * @param int $id Facility ID.
 */
$router->get('/facility/{id}', App\Controllers\FacilityController::class . '@getFacility');

/**
 * Route for creating a new facility
 * @method POST
 * @uri /create
 * @controller App\Controllers\FacilityController
 * @action createFacility
 * @description This route creates a new facility.
 */
$router->post('/create', App\Controllers\FacilityController::class . '@createFacility');

/**
 * Route for editing an existing facility
 * @method PUT
 * @uri /edit/{id}
 * @controller App\Controllers\FacilityController
 * @action editFacility
 * @description This route edits an existing facility.
 * @param int $id Facility ID.
 */
$router->put('/edit/{id}', App\Controllers\FacilityController::class . '@editFacility');

/**
 * Route for deleting a facility
 * @method DELETE
 * @uri /delete/{id}
 * @controller App\Controllers\FacilityController
 * @action deleteFacility
 * @description This route deletes a facility.
 * @param int $id Facility ID.
 */
$router->delete('/delete/{id}', App\Controllers\FacilityController::class . '@deleteFacility');

/**
 * Route for searching facilities
 * @method GET
 * @uri /search
 * @controller App\Controllers\FacilityController
 * @action searchFacility
 * @description This route searches for facilities based on query parameters.
 */
$router->get('/search', App\Controllers\FacilityController::class . '@searchFacility');