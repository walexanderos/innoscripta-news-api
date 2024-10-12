
# Innoscripta: News Aggregator API

## Table of Contents
- [Setup Instructions](#setup-instructions)
- [API Documentation](#api-documentation)

## Setup Instructions

### Prerequisites
- Ensure that Docker is installed on your machine.

### Running the Docker Environment

1. **Clone the Repository**
   ```bash
   git clone https://github.com/walexanderos/innoscripta-news-api.git
   cd innoscripta-news-api
   ```

2. **Build and Run the Containers**
   Run the following command to build and start the Docker containers:
   ```bash
   docker-compose up --build -d
   ```
    This will also run migration, clear cache and also generate swagger documentation

3. **Accessing the Application**
   After the containers have started, you can access the API at:
   ```
   http://localhost:8000
   ```

## API Documentation
The swagger documentation is automatically generated using annotations.

You can find the API documentation hosted on Swagger/OpenAPI here:
- [API Documentation](http://127.0.0.1:8000/api/documentation)
