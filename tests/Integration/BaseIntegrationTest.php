<?php declare(strict_types=1);

namespace Document\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseIntegrationTest extends WebTestCase
{
    public const BEARER = 'asdf';

    /**
     * @param array<string, string> $headers
     */
    protected function callEndpoint(string $method, string $endpoint, string $content = null, array $headers = []): Response
    {
        $client = static::createClient();
        $client->request($method, $endpoint, [], [], $headers + ['HTTP_USER_AGENT' => 'document-prague.local'], $content);

        return $client->getResponse();
    }

    /**
     * @return array<mixed>
     */
    protected function getResponseBody(Response $response): array
    {
        return json_decode((string) $response->getContent(), true);
    }
}
