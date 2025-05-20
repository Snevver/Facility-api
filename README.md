# Catering API Documentation

## Overview
The Catering API is a RESTful PHP API for managing facilities, locations, and tags. It was developed as part of an assessment for DTT Multimedia. The API allows you to create, read, update, delete, and search facilities, each of which can have multiple tags and is linked to a location.

## Setup Instructions

1. **Install dependencies:**
   ```bash
   composer install
   ```
2. **Configure the database:**
   - Edit `/config/config.php` with your MySQL credentials and database name.
   - Import the database schema from `App/Plugins/Db/import.sql` or `database_dump.sql`.
3. **Set up your web server:**
   - Place the project in your web server's root (e.g., `htdocs` for XAMPP).
   - Ensure URL rewriting is enabled (see `.htaccess`).
4. **Start the server and test:**
   - Visit `http://localhost/web_backend_test_catering_api` or your configured base URL.
   - Use the `/test` endpoint to verify the API is running.

## Endpoints

### 1. Get All Facilities
**GET** `/facilities`

Returns a list of all facilities, including their location and tags.

**Response:**
```json
[
  {
    "id": 1,
    "name": "Facility A",
    "creation_date": "2025-04-01",
    "location_id": 1,
    "city": "Amsterdam",
    "address": "Teststraat 1",
    "zip_code": "1234AB",
    "country_code": "NL",
    "phone_number": "+31 20 123 4567",
    "tags": ["Food", "Drinks"]
  },
  ...
]
```

### 2. Get a Specific Facility
**GET** `/facility/{id}`

Returns a single facility by ID.

**Response:**
```json
{
  "id": 1,
  "name": "Facility A",
  "creation_date": "2025-04-01",
  "location_id": 1,
  "city": "Amsterdam",
  "address": "Teststraat 1",
  "zip_code": "1234AB",
  "country_code": "NL",
  "phone_number": "+31 20 123 4567",
  "tags": ["Food", "Drinks"]
}
```

### 3. Create Facility
**POST** `/create`

**Request Body:**
```json
{
  "name": "New Facility",
  "location_id": 1,
  "tags": ["Food", "Catering"]
}
```

**Response:**
- Success: `{ "message": "Facility created successfully" }`
- Error: `{ "message": "Name and location_id are required fields" }`

### 4. Edit a Facility
**PUT** `/edit/{id}`

**Request Body:**
```json
{
  "name": "Updated Facility Name",
  "location_id": 2,
  "tags": ["Drinks", "Events"]
}
```

**Response:**
- Success: `{ "message": "Facility updated successfully" }`
- Error: `{ "message": "Facility not found" }`

### 5. Delete a Facility
**DELETE** `/delete/{id}`

**Response:**
- Success: `{ "message": "Facility deleted successfully" }`
- Error: `{ "message": "Facility not found" }`

### 6. Search for Facilities
**GET** `/search`

**Query Parameters:**
- `name` (optional): Filter by facility name (partial match)
- `tag` (optional): Filter by tag name (partial match)
- `city` (optional): Filter by city name (exact match)

**Example:**
```
GET /search?name=Facility&city=Amsterdam&tag=Food
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Facility A",
    "creation_date": "2025-04-01",
    "location_id": 1,
    "city": "Amsterdam",
    "address": "Teststraat 1",
    "zip_code": "1234AB",
    "country_code": "NL",
    "phone_number": "+31 20 123 4567",
    "tags": ["Food", "Drinks"]
  }
]
```

## Error Handling
The API returns standard HTTP status codes:
- **200 OK**: Request succeeded
- **201 Created**: Resource created successfully
- **400 Bad Request**: Invalid input data
- **404 Not Found**: Resource not found
- **500 Internal Server Error**: Server-side error

Error responses include a message:
```json
{ "message": "Description of the error" }
```

## Data Models

### Facility
| Field         | Type    | Description                         |
|---------------|---------|-------------------------------------|
| id            | integer | Unique identifier                   |
| name          | string  | Name of the facility                |
| creation_date | date    | Date when the facility was created  |
| location_id   | integer | ID of the associated location       |
| city          | string  | City of the location                |
| address       | string  | Street address                      |
| zip_code      | string  | ZIP/Postal code                     |
| country_code  | string  | Two-letter country code             |
| phone_number  | string  | Contact phone number                |
| tags          | array   | Array of tag names                  |

### Location
| Field         | Type    | Description                         |
|---------------|---------|-------------------------------------|
| id            | integer | Unique identifier                   |
| city          | string  | City name                           |
| address       | string  | Street address                      |
| zip_code      | string  | ZIP/Postal code                     |
| country_code  | string  | Two-letter country code             |
| phone_number  | string  | Contact phone number                |

### Tag
| Field | Type    | Description       |
|-------|---------|-------------------|
| id    | integer | Unique identifier |
| name  | string  | Name of the tag   |

---
This documentation file was updated to match the current codebase and API responses.

