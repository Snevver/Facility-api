# Catering API Documentation

## Overview
The Catering API is an assessment i had to make for my intern application at DTT Multimedia to show them what i have in store.

## Setup Instructions

Before using this API, make sure to install the required dependencies:

```bash
composer install
```

You may also need to configure your environment variables and database connection settings.

## Endpoints

### 1. Get All Facilities
Retrieves a list of all available facilities.

**Request:**
```
GET /facility
```

**Response:**
- Success (200 OK)
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
      "tags": "Food,Drinks"
    },
    {
      "id": 2,
      "name": "Facility B",
      "creation_date": "2025-04-02",
      "location_id": 2,
      "city": "Rotterdam",
      "address": "Placeholderstraat 99",
      "zip_code": "5678CD",
      "country_code": "NL",
      "phone_number": "+31 10 987 6543",
      "tags": "Catering"
    },
    {
        "id": 3,
        "name": "Facility C",
        "creation_date": "2025-04-03",
        "location_id": 3,
        "city": "Almere",
        "address": "Ditiseenstraatstraat 123",
        "zip_code": "0000EF",
        "country_code": "NL",
        "phone_number": "+31 30 765 4321",
        "tags": "Events,Food"
    }
  ]
  ```
- Error (404 Not Found)
  ```json
  {
    "message": "No facilities found"
  }
  ```

### 2. Get a Specific Facility
Retrieves details of a specific facility by ID.

**Request:**
```
GET /facility/{id}
```

**Parameters:**
- `id` (path parameter): The ID of the facility

**Response:**
- Success (200 OK)
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
    "tags": "Food,Drinks"
  }
  ```
- Error (404 Not Found)
  ```json
  {
    "message": "Facility not found"
  }
  ```

### 3. Create Facility
Creates a new facility.

**Request:**
```
POST /create
```

**Request Body:**
```json
{
  "name": "New Facility",
  "location_id": 1,
  "tags_id": [1, 2]
}
```

**Required Fields:**
- `name`: String - Name of the facility
- `location_id`: Integer - ID of the location

**Optional Fields:**
- `tags_id`: Array of integers - IDs of associated tags

**Response:**
- Success (201 Created)
  ```json
  {
    "message": "Facility created successfully"
  }
  ```
- Error (400 Bad Request)
  ```json
  {
    "message": "Name and location_id are required fields"
  }
  ```

### 4. Edit a Facility
Updates an existing facility.

**Request:**
```
PUT /edit/{id}
```

**Parameters:**
- `id` (path parameter): The ID of the facility to update

**Request Body:**
```json
{
  "name": "Updated Facility Name",
  "location_id": 2,
  "tags_id": [2, 3]
}
```

**Required Fields:**
- `name`: String - Updated name of the facility
- `location_id`: Integer - ID of the updated location

**Optional Fields:**
- `tags_id`: Array of integers - Updated IDs of associated tags

**Response:**
- Success (200 OK)
  ```json
  {
    "message": "Facility updated successfully"
  }
  ```
- Error (404 Not Found)
  ```json
  {
    "message": "Facility not found"
  }
  ```

### 5. Delete a Facility
Deletes a facility by ID.

**Request:**
```
DELETE /delete/{id}
```

**Parameters:**
- `id` (path parameter): The ID of the facility to delete

**Response:**
- Success (200 OK)
  ```json
  {
    "message": "Facility deleted successfully"
  }
  ```
- Error (404 Not Found)
  ```json
  {
    "message": "Facility not found"
  }
  ```

### 6. Search for Facilities
Searches for facilities based on various filters.

**Request:**
```
GET /search
```

**Query Parameters:**
- `name` (optional): Filter by facility name (partial match)
- `tag` (optional): Filter by tag name (partial match)
- `location` (optional): Filter by location ID (exact match)
- `zip_code` (optional): Filter by zip code (partial match)

**Example:**
```
GET /search?name=Facility&location=1&tag=Food
```

**Response:**
- Success (200 OK)
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
      "tags": "Food,Drinks"
    }
  ]
  ```
- Error (404 Not Found)
  ```json
  {
    "message": "No facilities found"
  }
  ```

## Error Handling
The API returns standard HTTP status codes:

- **200 OK**: Request succeeded
- **201 Created**: Resource created successfully
- **400 Bad Request**: Invalid input data
- **404 Not Found**: Resource not found
- **500 Internal Server Error**: Server-side error

Error responses include a message explaining the error:
```json
{
  "message": "Description of the error"
}
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
| tags          | string  | Comma-separated list of tags        |

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
This documentation file was partially created by AI to make it more clear

