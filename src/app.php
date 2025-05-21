<?php

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use KCC\DigitalSignage\Core\Display;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create app
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add view renderer
$renderer = new PhpRenderer(__DIR__ . '/../templates');

// Routes
$app->get('/', function (Request $request, Response $response) use ($renderer) {
    return $renderer->render($response, 'dashboard.php');
});

$app->get('/display/{location}/{type}[/{orientation}]', function (Request $request, Response $response, array $args) use ($renderer) {
    $location = $args['location'];
    $type = $args['type'];
    $isVertical = ($args['orientation'] ?? 'horizontal') === 'vertical';

    try {
        $display = new Display($location, $type, $isVertical);
        return $renderer->render($response, 'display.php', [
            'display' => $display
        ]);
    } catch (\Exception $e) {
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Content not found');
    }
});

// API Routes
$app->post('/api/upload', function (Request $request, Response $response) {
    // Handle file uploads
    $uploadedFiles = $request->getUploadedFiles();
    $location = $request->getParsedBody()['location'] ?? '';
    $type = $request->getParsedBody()['type'] ?? '';
    $orientation = $request->getParsedBody()['orientation'] ?? 'horizontal';

    if (empty($uploadedFiles['file'])) {
        return $response->withStatus(400)->withJson(['error' => 'No file uploaded']);
    }

    try {
        $file = $uploadedFiles['file'];
        $path = sprintf('%s/%s/%s/%s',
            $_ENV['CONTENT_BASE_PATH'],
            $location,
            $orientation,
            $type
        );

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file->moveTo($path . '/' . $file->getClientFilename());

        return $response->withJson(['success' => true]);
    } catch (\Exception $e) {
        return $response->withStatus(500)->withJson(['error' => $e->getMessage()]);
    }
});

$app->run(); 