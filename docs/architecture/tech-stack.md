# Technology Stack

## Backend

### Framework & Language
- **PHP**: 8.1+ (minimum version)
- **Laravel**: 10.x or 11.x (latest stable)
- **Composer**: Latest stable for dependency management

### Database
- **MariaDB**: Latest stable version
- **Driver**: MySQL driver (MariaDB compatible)

### Templating
- **Blade**: Laravel's native templating engine

## Frontend

### Client-Side Technologies
- **JavaScript**: ES6+ (modern JavaScript)
- **CSS**: CSS3 with responsive design
- **Build Tool**: Laravel Mix or Vite (Laravel default)

### Live Updates
- **Initial Implementation**: AJAX polling (1-2 second intervals)
- **Future Enhancement**: Laravel Echo + Soketi/Pusher for WebSockets

## Development Tools

### Testing
- **PHPUnit**: For unit and feature tests (included with Laravel)
- **Codeception**: For browser/acceptance tests
- **Laravel Dusk**: Alternative browser testing (if needed)

### Code Quality
- **PSR-12**: PHP coding standards
- **Laravel Pint**: Code formatting tool
- **PHPStan/Larastan**: Static analysis (optional)

## Server Requirements

### Minimum Requirements
- PHP 8.1+
- MariaDB 10.3+
- Composer 2.x
- Node.js 16+ (for frontend asset compilation)
- NPM or Yarn

### PHP Extensions Required
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath

## Deployment Considerations

### Environment
- **Development**: Local (php artisan serve, Laravel Valet, or Docker)
- **Production**: Apache/Nginx with PHP-FPM

### Session & Cache
- **Development**: File-based
- **Production**: Redis (recommended for scaling)

## Version Control
- **Git**: For source control
- **.gitignore**: Laravel default, exclude vendor/, node_modules/, .env

## Dependencies (Key Packages)

### Laravel Core Packages (Included)
- `laravel/framework`
- `laravel/tinker`

### Development Dependencies
- `phpunit/phpunit`
- `codeception/codeception`
- `laravel/pint`
- `fakerphp/faker` (for seeders)

### Production Dependencies
- None required beyond Laravel core initially

## Browser Support

### Target Browsers
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Mobile Safari (iOS 13+)
- Chrome Mobile (Android 10+)

### Responsive Breakpoints
- Mobile: 320px - 767px
- Tablet: 768px - 1024px
- Laptop: 1025px - 1440px
- Large Screen/Projector: 1441px+

## Notes

- Laravel version should be confirmed during project initialization
- Prefer stable, LTS versions where available
- Keep dependencies minimal to reduce complexity
- All versions should be locked in `composer.json` for consistency
