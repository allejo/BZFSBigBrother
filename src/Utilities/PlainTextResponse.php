<?php declare(strict_types=1);

namespace App\Utilities;

use Symfony\Component\HttpFoundation\Response;

class PlainTextResponse extends Response
{
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, $headers);

        $this->headers->set('Content-Type', 'text/plain');
    }
}
