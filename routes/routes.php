<?php

/** @var Bramus\Router\Router $router */

// Define routes here
$router->get('/facility', App\Controllers\FacilityController::class . '@getAllFacilities');
$router->get('/facility/{id}', App\Controllers\FacilityController::class . '@getFacility');
$router->post('/create', App\Controllers\FacilityController::class . '@createFacility');
$router->put('/edit/{id}', App\Controllers\FacilityController::class . '@editFacility');
$router->delete('/delete/{id}', App\Controllers\FacilityController::class . '@deleteFacility');
$router->get('/search', App\Controllers\FacilityController::class . '@searchFacility');