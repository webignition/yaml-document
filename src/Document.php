<?php

declare(strict_types=1);

namespace webignition\YamlDocument;

use Symfony\Component\Yaml\Parser as SymfonyYamlParser;

class Document
{
    private const DOCUMENT_START = '---';
    private const DOCUMENT_END = '...';

    private const DOCUMENT_START_LENGTH = 3;
    private const DOCUMENT_END_LENGTH = 3;

    public function __construct(
        private string $content = ''
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    public function isEmpty(): bool
    {
        return '' === $this->content;
    }

    public function append(string $content): self
    {
        $new = clone $this;
        $new->content .= $content;

        return $new;
    }

    public function parse(?SymfonyYamlParser $parser = null): mixed
    {
        if (null === $parser) {
            $parser = new SymfonyYamlParser();
        }

        return $parser->parse($this->content);
    }

    public static function isDocumentStart(string $line): bool
    {
        if (self::DOCUMENT_START_LENGTH > strlen($line)) {
            return false;
        }

        return self::DOCUMENT_START === substr($line, 0, self::DOCUMENT_START_LENGTH);
    }

    public static function isDocumentEnd(string $line): bool
    {
        if (self::DOCUMENT_END_LENGTH > strlen($line)) {
            return false;
        }

        return self::DOCUMENT_END === substr($line, 0, self::DOCUMENT_END_LENGTH);
    }
}
