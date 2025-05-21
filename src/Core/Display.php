<?php

declare(strict_types=1);

namespace KCC\DigitalSignage\Core;

class Display
{
    private string $location;
    private string $contentType;
    private bool $isVertical;
    /** @var array<int, string> $content */
    private array $content;
    private string $basePath;

    public function __construct(string $location, string $contentType, bool $isVertical = false)
    {
        $this->location = $location;
        $this->contentType = $contentType;
        $this->isVertical = $isVertical;
        $this->content = [];
        $this->basePath = $_ENV['CONTENT_BASE_PATH'] ?? __DIR__ . '/../../content';
        $this->loadContent();
    }

    private function loadContent(): void
    {
        $path = $this->getBasePath();
        if (!is_dir($path)) {
            throw new \RuntimeException("Content directory not found: {$path}");
        }

        $scanResult = scandir($path);
        if ($scanResult === false) {
            throw new \RuntimeException("Failed to scan directory: {$path}");
        }

        $files = array_diff($scanResult, ['.', '..', 'index.php']);
        /** @var array<int, string> $filteredFiles */
        $filteredFiles = array_values(array_filter($files, function (string $file): bool {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'mp4'], true);
        }));
        $this->content = $filteredFiles;
    }

    private function getBasePath(): string
    {
        $orientation = $this->isVertical ? 'vertical' : 'horizontal';
        return sprintf(
            '%s/%s/%s/%s',
            $this->basePath,
            $this->location,
            $orientation,
            $this->contentType
        );
    }

    public function getCurrentContent(): ?string
    {
        if (empty($this->content)) {
            return null;
        }
        return $this->content[0];
    }

    public function getContentPath(): string
    {
        $current = $this->getCurrentContent();
        if ($current === null) {
            return '';
        }
        return $this->getBasePath() . '/' . $current;
    }

    public function getContentType(): string
    {
        $current = $this->getCurrentContent();
        if ($current === null) {
            return '';
        }
        return strtolower(pathinfo($current, PATHINFO_EXTENSION));
    }

    public function isVideo(): bool
    {
        return $this->getContentType() === 'mp4';
    }
} 