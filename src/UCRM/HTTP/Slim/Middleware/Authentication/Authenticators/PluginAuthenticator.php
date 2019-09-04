<?php
declare(strict_types=1);

namespace UCRM\HTTP\Slim\Middleware\Authentication\Authenticators;

//use Psr\Container\ContainerInterface as Container;
//use Psr\Http\Message\ServerRequestInterface as Request;
//use Psr\Http\Message\ResponseInterface as Response;


use MVQN\HTTP\Slim\Middleware\Authentication\Authenticators\Authenticator;
use MVQN\HTTP\Twig\Extensions\QueryStringRoutingExtension;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UCRM\Common\Log;
use UCRM\Common\Plugin;
use UCRM\HTTP\Twig\Extensions\PluginExtension;
use UCRM\Sessions\Session;

class PluginAuthenticator extends Authenticator
{
    /**
     * The default allowed User Groups.
     */
    protected const DEFAULT_ALLOWED_USER_GROUPS = [ "Admin Group" ];

    /**
     * @var Container A local reference to the Slim Framework DI Container.
     */
    //protected $container;

    /**
     * @var array An array of allowed User Groups in the UCRM for which to validate the currently authenticated user.
     */
    //protected $allowedUserGroups;
    /** @var callable|null */
    protected $verification;
    protected $allowed;


    /**
     * PluginAuthentication constructor.
     *
     * @param Container $container
     * @param callable|null $verification
     */
    public function __construct(callable $verification = null)
    {
        //$this->container = $container;
        $this->verification = $verification;
    }


    /**
     * Middleware invokable class
     *
     * @param Request $request The current PSR-7 Request object.
     * @param Response $response The current PSR-7 Response object.
     * @param callable $next The next middleware for which to pass control if this middleware does not fail.
     *
     * @return Response Returns a PSR-7 Response object.
     * @throws \UCRM\Common\Exceptions\PluginNotInitializedException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        // IF this Plugin is in development mode, THEN skip this Middleware!
        if(Plugin::mode() === Plugin::MODE_DEVELOPMENT)
            return $next($request->withAttribute("authenticated", true), $response);

        // Allow localhost!
        if($request->getUri()->getHost() === "localhost")
            return $next($request->withAttribute("authenticated", true), $response);

        // IF a Session is not already started, THEN start one!
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        // Get the currently authenticated User, while also capturing the actual '/current-user' response!
        $user = Session::getCurrentUser();

        // Display an error if no user is authenticated!
        //if(!$user)
        //    Log::http("No User is currently Authenticated!", Log::HTTP, 401);

        $valid = true;

        if($this->verification !== null && is_callable($this->verification) && !($this->verification)($user))
        {
            //Log::http("Currently Authenticated User is not allowed!", 401);
            //http_response_code(401);
            //exit();
            $valid = false;
        }

        // Set the current session user on the container, for later use in the application.
        //$this->container["sessionUser"] = $user;
        //PluginExtension::setGlobal("sessionUser", $user);
        QueryStringRoutingExtension::addGlobal("sessionUser", $user);
        $request = $request
            ->withAttribute("sessionUser", $user)
            ->withAttribute("authenticated", $valid);

        // If a valid user is authenticated and
        return $next($request, $response);
    }
}
