# PDF <-> Word Converter (Laravel + Blade + Livewire)

These files are an overlay for a fresh Laravel project.

## Setup
    composer create-project laravel/laravel pdf-word-converter
    cd pdf-word-converter
    composer require livewire/livewire
    # copy the files from this folder over the project (Dockerfile, .dockerignore, app/, resources/, routes/)

In bootstrap/app.php add inside ->withMiddleware(function (Middleware $middleware) { ... }):

    $middleware->trustProxies(at: '*');

(needed so Livewire uploads work behind the AWS HTTPS load balancer)

## Run with Docker
    docker build -t pdf-word-converter .
    docker run -p 8080:80 pdf-word-converter
    # open http://localhost:8080
