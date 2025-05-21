<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use KCC\DigitalSignage\Core\Display;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

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
$app->get('/', function (Request $request, Response $response) use ($renderer): Response {
    return $renderer->render($response, 'dashboard.php');
});

$app->get('/display/{location}/{type}[/{orientation}]', function (Request $request, Response $response, array $args) use ($renderer): Response {
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
$app->post('/api/upload', function (Request $request, Response $response): Response {
    // Handle file uploads
    $uploadedFiles = $request->getUploadedFiles();
    $parsedBody = $request->getParsedBody();
    
    if (!is_array($parsedBody)) {
        return $response->withStatus(400)->withJson(['error' => 'Invalid request body']);
    }

    $location = $parsedBody['location'] ?? '';
    $type = $parsedBody['type'] ?? '';
    $orientation = $parsedBody['orientation'] ?? 'horizontal';

    if (empty($uploadedFiles['file'])) {
        return $response->withStatus(400)->withJson(['error' => 'No file uploaded']);
    }

    try {
        $file = $uploadedFiles['file'];
        if (!$file instanceof UploadedFileInterface) {
            return $response->withStatus(400)->withJson(['error' => 'Invalid file upload']);
        }

        $uploadDir = sprintf(
            '%s/%s/%s/%s',
            $_ENV['CONTENT_BASE_PATH'] ?? __DIR__ . '/../content',
            $location,
            $orientation,
            $type
        );

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \RuntimeException('Failed to create upload directory');
            }
        }

        $targetPath = $uploadDir . '/' . $file->getClientFilename();
        $file->moveTo($targetPath);

        return $response->withJson(['success' => true]);
    } catch (\Exception $e) {
        return $response->withStatus(500)->withJson(['error' => $e->getMessage()]);
    }
});

$app->run(); 