<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-21
 * Time: 14:41
 */

/**
 * make composer autoload
 */
require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ],
    /** Server URI. Make Same as uri of your server service  */
    'server' => 'localhost:8080',
    'guzzle' => function ($container) {
        return new \GuzzleHttp\Client([
            'base_uri' => $container['server'], //this is variable from above
        ]);
    },
    /**
     * @see https://www.slimframework.com/docs/features/templates.html
     */
    'view'   => function ($container) {
        $view = new \Slim\Views\Twig(__DIR__ . '/templates');
        // Instantiate and add Slim specific extension
        $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
        $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

        return $view;
    },
]);


/**
 * routes
 */
$app->get('/', function ($request, $response) {
    try {
        //$this is container from above
        /** @var $apiResponse \GuzzleHttp\Psr7\Response */
        $apiResponse = $this->guzzle->get('/', [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
        ]);
    } catch (\GuzzleHttp\Exception\ServerException $e) {
        if ($e->getCode() == 404) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        throw $e;
    }

    $posts = json_decode($apiResponse->getBody(), true);
    /** @see https://www.slimframework.com/docs/features/templates.html */
    return $this->view->render($response, 'posts.twig', [
        'posts' => $posts,
    ]);
});

$app->get('/{id:\d+}', function ($request, $response, $arguments) {

    try {//$this is container from above
        /** @var $apiResponse \GuzzleHttp\Psr7\Response */
        $apiResponse = $this->guzzle->get('/' . $arguments['id'], [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
        ]);
    } catch (\GuzzleHttp\Exception\ServerException $e) {
        if ($e->getCode() == 404) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        throw $e;
    }

    $post = json_decode($apiResponse->getBody());

    /** @see https://www.slimframework.com/docs/features/templates.html */
    return $this->view->render($response, 'post.twig', [
        'post' => $post,
    ]);
});

$app->run();