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

// Helper function for JSON responses
$jsonResponse = function (Response $response, mixed $data, int $status = 200): Response {
    $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
};

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
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Content not found');
    }
});

// API Routes
$app->post('/api/upload', function (Request $request, Response $response) use ($jsonResponse): Response {
    // Handle file uploads
    $uploadedFiles = $request->getUploadedFiles();
    $parsedBody = $request->getParsedBody();
    
    if (!is_array($parsedBody)) {
        return $jsonResponse($response, ['error' => 'Invalid request body'], 400);
    }

    $location = $parsedBody['location'] ?? '';
    $type = $parsedBody['type'] ?? '';
    $orientation = $parsedBody['orientation'] ?? 'horizontal';

    if (empty($uploadedFiles['file'])) {
        return $jsonResponse($response, ['error' => 'No file uploaded'], 400);
    }

    try {
        $file = $uploadedFiles['file'];
        if (!$file instanceof UploadedFileInterface) {
            return $jsonResponse($response, ['error' => 'Invalid file upload'], 400);
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

        return $jsonResponse($response, ['success' => true]);
    } catch (\Exception $e) {
        return $jsonResponse($response, ['error' => $e->getMessage()], 500);
    }
});

$app->run(); 