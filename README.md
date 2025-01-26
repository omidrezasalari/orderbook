# Wallex - challenge - Orderbook 

This project provides an API for placing orders (buy/sell).

## Prerequisites

- Docker
- Docker Compose

## Setup

1. Clone or download the repository.

2. Navigate to the project directory in your terminal.

3. **Create a `.env` file** (if not already present) with your environment variables for configuration.

4. Build and start the Docker containers using the following command:

   ```bash
   docker-compose up -d

The API will be available at http://localhost:3030.

API Documentation : ./documentation

Place an Order Endpoint: POST /api/v1/orders

Used for placing buy or sell orders.

### Request Body:
```shell
{
"type": "sell",      // Order type ("buy" or "sell")
"price": 100,        // Order price
"quantity": 2        // Order quantity
}
```
#### cURL Command to Place an Order:

```shell
curl -X POST "http://localhost:3030/api/v1/orders" \
     -H "Content-Type: application/json" \
     -d '{"type": "sell", "price": 100, "quantity": 2}'
```
### Response :

````shell
{
  "message": "Order placed successfully.",
  "order": {
    "type": "sell",
    "price": 100,
    "quantity": 2
  }
````

### Process Orders Command:

```shell
php artisan orders:process
```

