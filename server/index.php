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

$app = new \Slim\App();

/**
 * my fake database, skip this part or @see http://wern-ancheta.com/blog/2016/01/28/generating-fake-data-in-php-with-faker/
 */

$faker = Faker\Factory::create();
$posts = [];
for ($i = 1; $i <= 50; $i++) {
    $posts[$i] = [
        'id'         => $i,
        'title'      => $faker->sentence(3),
        'created_at' => $faker->dateTime->format('Y-m-d'),
        'updated_at' => $faker->dateTime->format('Y-m-d'),
        'author'     => $faker->userName,
        'content'    => $faker->paragraphs(random_int(2, 5)),
        'thumbnail'  => $faker->imageUrl(),
    ];
}

/**
 * routes
 */
$app->get('/', function ($request, $response) use ($posts) {
    return $response->withJson($posts);
});

$app->get('/{id:\d+}', function ($request, $response, $arguments) use ($posts) {
    if (!isset($posts[$arguments['id']])) {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    return $response->withJson($posts[$arguments['id']]);
});


/**
 * run application
 */
$app->run();
