<?php

namespace App\Models;

class Facility {
    public int $id;
    public string $name;
    public string $creation_date;
    public int $location_id;
    public string $city;
    public string $address;
    public string $zip_code;
    public string $country_code;
    public string $phone_number;
    public array $tags = [];
}