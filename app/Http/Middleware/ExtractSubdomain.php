<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Symfony\Component\HttpFoundation\Response;

class ExtractSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next)
    {
        // Extract the subdomain from the request host
        $subdomain = explode('.', $request->getHost())[0];

        // Load subdomain configuration
        $subdomainsConfig = config('subdomain.subdomain');
        $defaultConfig = config('subdomain.default');

        // Check if subdomain exists in the configuration
        if (array_key_exists($subdomain, $subdomainsConfig)) {
            $dbConfig = $subdomainsConfig[$subdomain];
        } else {
            // Use default configuration if subdomain not found
            $dbConfig = $defaultConfig;
        }

        // Dynamically set database connection
        config([
            'database.connections.mysql.host' => $dbConfig['host'],
            'database.connections.mysql.database' => $dbConfig['database'],
            'database.connections.mysql.username' => $dbConfig['username'],
            'database.connections.mysql.password' => $dbConfig['password'],
        ]);

        \DB::purge('mysql'); // Clear previous database connection
        \DB::reconnect('mysql'); // Reconnect with the new configuration

        return $next($request);
    }




}
