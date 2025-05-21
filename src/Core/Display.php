<?php

namespace KCC\DigitalSignage\Core;

class Display
{
    private string $location;
    private string $contentType;
    private bool $isVertical;
    private array $content;

    public function __construct(string $location, string $contentType, bool $isVertical = false)
    {
        $this->location = $location;
        $this->contentType = $contentType;
        $this->isVertical = $isVertical;
        $this->content = [];
        $this->loadContent();
    }

    private function loadContent(): void
    {
        $path = $this->getContentPath();
        if (!is_dir($path)) {
            throw new \RuntimeException("Content directory not found: {$path}");
        }

        $files = array_diff(scandir($path), ['.', '..', 'index.php']);
        $this->content = array_values(array_filter($files, function($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'mp4']);
        }));
    }

    private function getContentPath(): string
    {
        $base = $_ENV['CONTENT_BASE_PATH'] ?? __DIR__ . '/../../content';
        $orientation = $this->isVertical ? 'vertical' : 'horizontal';
        return sprintf('%s/%s/%s/%s', 
            $base,
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
        return $current ? $this->getContentPath() . '/' . $current : '';
    }

    public function getContentType(): string
    {
        $current = $this->getCurrentContent();
        return $current ? strtolower(pathinfo($current, PATHINFO_EXTENSION)) : '';
    }

    public function isVideo(): bool
    {
        return $this->getContentType() === 'mp4';
    }
} 