# Pour Test Competition App

A Laravel-based web application for managing bartender Pour Test competitions.

## Tech Stack

- **Backend**: PHP 8.2, Laravel 11.x
- **Database**: MariaDB (latest)
- **Web Server**: Nginx
- **Containerization**: Docker & Docker Compose

## Prerequisites

- Docker Engine 20.10+
- Docker Compose V2

## Quick Start

### 1. Clone the repository
```bash
git clone <repository-url>
cd helloworld
```

### 2. Build and start Docker containers
```bash
docker compose build
docker compose up -d
```

### 3. Install dependencies (if needed)
```bash
docker compose exec app composer install
```

### 4. Set up environment file
The `.env` file is already configured for Docker. Verify the database settings:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=pourtest
DB_USERNAME=pourtest_user
DB_PASSWORD=secret
```

### 5. Run database migrations
```bash
docker compose exec app php artisan migrate
```

### 6. Access the application
- **Application**: http://localhost:8000
- **PHPMyAdmin**: http://localhost:8080
  - Server: db
  - Username: pourtest_user
  - Password: secret

## Docker Services

### Application (app)
- PHP 8.2-FPM
- Composer installed
- Port: 9000 (internal)

### Web Server (webserver)
- Nginx Alpine
- Port: 8000 (external) → 80 (internal)

### Database (db)
- MariaDB latest
- Port: 3306
- Database: pourtest
- User: pourtest_user
- Password: secret

### PHPMyAdmin (phpmyadmin)
- Database management interface
- Port: 8080

## Useful Commands

### Start containers
```bash
docker compose up -d
```

### Stop containers
```bash
docker compose down
```

### View logs
```bash
docker compose logs -f app
docker compose logs -f webserver
```

### Run artisan commands
```bash
docker compose exec app php artisan <command>
```

### Run composer commands
```bash
docker compose exec app composer <command>
```

### Access application shell
```bash
docker compose exec app sh
```

### Run migrations
```bash
docker compose exec app php artisan migrate
```

### Seed database
```bash
docker compose exec app php artisan db:seed
```

### Fresh migration (drop all tables and re-migrate)
```bash
docker compose exec app php artisan migrate:fresh
```

### Run tests
```bash
docker compose exec app php artisan test
```

## Development Workflow

1. Make code changes in your local editor
2. Changes are automatically reflected in the container (volume mounted)
3. If you add new dependencies, run:
   ```bash
   docker compose exec app composer install
   ```
4. If you create new migrations, run:
   ```bash
   docker compose exec app php artisan migrate
   ```

## Project Structure

```
.
├── app/                    # Application code
├── bootstrap/              # Laravel bootstrap files
├── config/                 # Configuration files
├── database/               # Migrations, seeders, factories
├── docker/                 # Docker configuration
│   ├── nginx/             # Nginx config
│   └── php/               # PHP config
├── docs/                   # Project documentation
│   ├── architecture/      # Architecture docs
│   ├── prd/               # Product requirements (epics)
│   └── stories/           # User stories
├── public/                 # Public web root
├── resources/              # Views, assets
├── routes/                 # Route definitions
├── storage/                # Storage files
├── tests/                  # Test files
├── .env                    # Environment configuration
├── docker-compose.yml      # Docker Compose configuration
├── Dockerfile              # PHP application container
└── README.md              # This file
```

## Troubleshooting

### Cannot connect to database
1. Ensure all containers are running: `docker compose ps`
2. Check database logs: `docker compose logs db`
3. Verify database credentials in `.env`

### Permission errors
```bash
docker compose exec app chown -R pourtest:www-data /var/www/storage
docker compose exec app chmod -R 775 /var/www/storage
```

### Clear Laravel cache
```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
```

## Next Steps

Refer to the project documentation in the `docs/` directory:
- [Architecture Documentation](docs/architecture/index.md)
- [Product Requirements](docs/prd.md)
- [User Stories](docs/stories/)

## License

[Your License Here]
