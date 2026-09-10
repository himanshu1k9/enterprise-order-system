<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Import Classes
|--------------------------------------------------------------------------
|
| We import all classes that are required to bootstrap the application.
|
*/

use App\Application;
use App\Auth\CurrentUser;
use App\Auth\SessionManager;
use App\Container\Container;
use App\Database\Database;
use App\Exceptions\ExceptionHandler;
use App\Http\Kernel;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\ProductDeleteMiddleware;
use App\Http\Middleware\ProductWriteMiddleware;
use App\Http\Request;
use App\Http\RequestId;
use App\Logging\Logger;
use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\PasswordResetTokenRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Routing\Router;
use App\Security\CsrfManager;
use App\Security\RateLimiter;
use Dotenv\Dotenv;


/*
|--------------------------------------------------------------------------
| Composer Autoload
|--------------------------------------------------------------------------
|
| This loads Composer's autoloader.
|
| Without this, PHP would not automatically find classes from:
|
| - App\
| - Dotenv
| - Other Composer packages
|
*/

require_once __DIR__ . '/../vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| Load Environment Variables
|--------------------------------------------------------------------------
|
| .env contains environment-specific configuration such as:
|
| DB_HOST
| DB_NAME
| DB_USER
| DB_PASSWORD
|
| safeLoad() means the application will not crash if .env
| is missing.
|
*/

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');

$dotenv->safeLoad();


/*
|--------------------------------------------------------------------------
| Error Reporting
|--------------------------------------------------------------------------
|
| DEVELOPMENT ONLY
|
| During development we want PHP errors to be visible.
|
| IMPORTANT:
| In production:
|
| display_errors = 0
|
| Errors should instead be written to logs.
|
*/

error_reporting(E_ALL);

ini_set('display_errors', '1');

ini_set('display_startup_errors', '1');


/*
|--------------------------------------------------------------------------
| Create Dependency Injection Container
|--------------------------------------------------------------------------
|
| The Container is responsible for creating and managing
| application dependencies.
|
| Example:
|
| ProductController
|       ↓
| ProductService
|       ↓
| ProductRepositoryInterface
|       ↓
| ProductRepository
|
*/

$container = new Container();


/*
|--------------------------------------------------------------------------
| Database / PDO
|--------------------------------------------------------------------------
|
| We register PDO as a singleton.
|
| Why singleton?
|
| We generally want one database connection instance
| during the application lifecycle.
|
*/

$container->singleton(
    PDO::class,
    function () {

        $database = new Database();

        return $database->connection();
    }
);


/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
|
| Router is registered as a singleton because the same router
| instance will be used while the application is running.
|
*/

$container->singleton(
    Router::class,
    function () {

        return new Router();
    }
);


/*
|--------------------------------------------------------------------------
| HTTP Request
|--------------------------------------------------------------------------
|
| Request represents the current HTTP request.
|
| Example:
|
| GET /products?page=2
|
| The Request object gives us access to:
|
| - method
| - URL
| - headers
| - query parameters
| - body
|
*/

$container->singleton(
    Request::class,
    function () {

        return new Request();
    }
);


/*
|--------------------------------------------------------------------------
| Application
|--------------------------------------------------------------------------
|
| Application is resolved through the Container.
|
| The Container will inject:
|
| Container
| Kernel
| Request
| RequestId
|
*/

$container->bind(
    Application::class,
    Application::class
);


/*
|--------------------------------------------------------------------------
| Repository Interface Bindings
|--------------------------------------------------------------------------
|
| These are VERY IMPORTANT.
|
| Our services depend on interfaces instead of concrete classes.
|
| Example:
|
| ProductService
|      ↓
| ProductRepositoryInterface
|
| The Container needs to know:
|
| ProductRepositoryInterface
|      ↓
| ProductRepository
|
*/

$container->bind(
    ProductRepositoryInterface::class,
    ProductRepository::class
);

$container->bind(
    UserRepositoryInterface::class,
    UserRepository::class
);

$container->bind(
    PasswordResetTokenRepositoryInterface::class,
    PasswordResetTokenRepository::class
);

$container->singleton(PermissionMiddleware::class, function() use($container) {
    return new PermissionMiddleware($container->get(CurrentUser::class));
});


/*
|--------------------------------------------------------------------------
| Session Manager
|--------------------------------------------------------------------------
|
| SessionManager is responsible for:
|
| - starting sessions
| - login
| - logout
| - retrieving user ID
|
| We register it BEFORE loading routes.
|
| WHY?
|
| AuthController
|      ↓
| AuthService
|      ↓
| SessionManager
|
| Routes resolve AuthController while routes are being loaded.
|
*/

$container->singleton(
    SessionManager::class,
    function () {
        return new SessionManager();
    }
);


/*
|--------------------------------------------------------------------------
| CSRF Manager
|--------------------------------------------------------------------------
|
| CsrfManager depends on SessionManager.
|
| Therefore SessionManager must already be registered.
|
*/

$container->singleton(
    CsrfManager::class,
    function () use ($container) {

        return new CsrfManager(
            $container->get(SessionManager::class)
        );
    }
);


/*
|--------------------------------------------------------------------------
| Rate Limiter
|--------------------------------------------------------------------------
|
| RateLimiter has a primitive constructor dependency:
|
| string $storagePath
|
| The Container cannot automatically resolve primitive values
| such as:
|
| string
| int
| bool
|
| Therefore we MUST explicitly bind RateLimiter.
|
| IMPORTANT:
|
| This binding MUST happen BEFORE routes are loaded.
|
| Otherwise:
|
| routes
|   ↓
| AuthController
|   ↓
| AuthService
|   ↓
| RateLimiter
|   ↓
| Container tries to resolve "string"
|   ↓
| ERROR:
| Class string does not exist
|
*/

$container->singleton(
    RateLimiter::class,
    function () {

        return new RateLimiter(
            dirname(__DIR__) . '/storage/rate-limit'
        );
    }
);


/*
|--------------------------------------------------------------------------
| Request ID
|--------------------------------------------------------------------------
|
| RequestId gives every request a unique identifier.
|
| Example:
|
| X-Request-ID: 7f8a9c...
|
| It is useful for tracing a request through logs.
|
*/

$container->singleton(
    RequestId::class,
    function () {

        return new RequestId();
    }
);


/*
|--------------------------------------------------------------------------
| Logger
|--------------------------------------------------------------------------
|
| Logger depends on RequestId.
|
| Therefore RequestId must already be registered.
|
*/

$container->singleton(
    Logger::class,
    function () use ($container) {

        return new Logger(
            dirname(__DIR__) . '/storage/logs/app.log',
            $container->get(RequestId::class)
        );
    }
);


/*
|--------------------------------------------------------------------------
| Exception Handler
|--------------------------------------------------------------------------
|
| ExceptionHandler depends on:
|
| - Logger
| - RequestId
|
| Both have already been registered above.
|
*/

$container->singleton(
    ExceptionHandler::class,
    function () use ($container) {

        return new ExceptionHandler(
            $container->get(Logger::class),
            $container->get(RequestId::class)
        );
    }
);


/*
|--------------------------------------------------------------------------
| Current User
|--------------------------------------------------------------------------
|
| CurrentUser depends on:
|
| SessionManager
| UserRepositoryInterface
|
| Both dependencies are already registered.
|
| CurrentUser is a singleton because we want the same
| current-user service during the request lifecycle.
|
*/

$container->singleton(
    CurrentUser::class,
    function () use ($container) {

        return new CurrentUser(
            $container->get(SessionManager::class),
            $container->get(UserRepositoryInterface::class)
        );
    }
);


/*
|--------------------------------------------------------------------------
| Product Write Middleware
|--------------------------------------------------------------------------
|
| ProductWriteMiddleware depends on CurrentUser.
|
| Allowed roles:
|
| - SUPER_ADMIN
| - ADMIN
| - STAFF
|
*/

$container->singleton(
    ProductWriteMiddleware::class,
    function () use ($container) {

        return new ProductWriteMiddleware(
            $container->get(CurrentUser::class)
        );
    }
);


/*
|--------------------------------------------------------------------------
| Product Delete Middleware
|--------------------------------------------------------------------------
|
| ProductDeleteMiddleware depends on CurrentUser.
|
| Allowed roles:
|
| - SUPER_ADMIN
| - ADMIN
|
*/

$container->singleton(
    ProductDeleteMiddleware::class,
    function () use ($container) {

        return new ProductDeleteMiddleware(
            $container->get(CurrentUser::class)
        );
    }
);


/*
|--------------------------------------------------------------------------
| Load Routes
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| ALL dependencies required by controllers/middleware used by
| routes must already be registered above.
|
| For example:
|
| routes/web.php
|       ↓
| AuthController
|       ↓
| AuthService
|       ↓
| RateLimiter
|
| Therefore RateLimiter MUST be registered before this section.
|
*/

$router = $container->get(Router::class);
$routes = require __DIR__ . '/../routes/web.php';
$routes($router, $container);

/*
|--------------------------------------------------------------------------
| Register Kernel
|--------------------------------------------------------------------------
|
| Kernel is the central HTTP request processor.
|
| Flow:
|
| Application
|      ↓
| Kernel
|      ↓
| Global Middleware
|      ↓
| Router
|      ↓
| Route Middleware
|      ↓
| Controller
|
*/

$container->singleton(
    Kernel::class,
    function () use ($container) {

        return new Kernel(
            $container,
            $container->get(Router::class),
            $container->get(ExceptionHandler::class)
        );
    }
);


/*
|--------------------------------------------------------------------------
| IMPORTANT NOTE
|--------------------------------------------------------------------------
|
| We intentionally DO NOT register AuthMiddleware globally here.
|
| Authentication is route-specific.
|
| Example:
|
| POST /products
|      ↓
| AuthMiddleware
|
| GET /products
|      ↓
| Public
|
| This allows us to protect only the routes that need authentication.
|
*/


/*
|--------------------------------------------------------------------------
| Old / Unused SessionHandler Binding
|--------------------------------------------------------------------------
|
| Your application currently uses:
|
| SessionManager
|
| NOT:
|
| SessionHandler
|
| Therefore this old binding has been removed/commented out.
|
| Keeping this comment helps us remember why it should not be added
| again accidentally.
|
*/

// $container->singleton(
//     SessionHandler::class,
//     function () {
//         return new SessionHandler();
//     }
// );


/*
|--------------------------------------------------------------------------
| Bootstrap Complete
|--------------------------------------------------------------------------
|
| At this point the container knows how to resolve:
|
| PDO
| Router
| Request
| Application
| ProductRepositoryInterface
| UserRepositoryInterface
| SessionManager
| CsrfManager
| RateLimiter
| RequestId
| Logger
| ExceptionHandler
| CurrentUser
| ProductWriteMiddleware
| ProductDeleteMiddleware
| Kernel
|
| The application can now be started by public/index.php.
|
*/