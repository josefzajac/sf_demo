<?php declare(strict_types=1);

namespace Document\Tests\Integration\Modules\File;

use Document\Controller\FileController;
use Document\Tests\Integration\BaseIntegrationTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\Depends;

#[CoversClass(FileController::class)]
#[CoversMethod(FileController::class, 'upload')]
#[CoversMethod(FileController::class, 'update')]
#[CoversMethod(FileController::class, 'delete')]
class FileTest extends BaseIntegrationTest
{
    public function testWrongCredentials(): void
    {
        $response = $this->callEndpoint(method: 'post', endpoint: '/documents', headers: ['HTTP_USER_AGENT' => 'xxx']);

        self::assertSame(403, $response->getStatusCode());
    }

    public function testUploadFile(): void
    {
        $response = $this->callEndpoint('post', '/documents', self::getTestJson());

        self::assertSame(201, $response->getStatusCode());
        self::assertTrue($response->headers->has('Location'));
        self::assertSame('/data/images/origin/2021-01/filename.jpg', $response->headers->get('Location'));
        self::assertSame('', $response->getContent());
    }

    #[Depends('testUploadFile')]
    public function testInvalidExisting(): void
    {
        $response = $this->callEndpoint('post', '/documents', self::getInvalidJson());

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('{"title":"date: This value is not a valid date.","status":400}', $response->getContent());
    }

    #[Depends('testInvalidExisting')]
    public function testUploadExisting(): void
    {
        $response = $this->callEndpoint('post', '/documents', self::getTestJson());

        self::assertSame(201, $response->getStatusCode());
        self::assertTrue($response->headers->has('Location'));
        self::assertSame('/data/images/origin/2021-01/filename.jpg', $response->headers->get('Location'));
        self::assertSame('', $response->getContent());
    }

    #[Depends('testUploadExisting')]
    public function testUpdateFile(): void
    {
        $response = $this->callEndpoint('put', '/documents/images/origin/2021-01/filename.jpg', self::getTestJson());

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', $response->getContent());
    }

    #[Depends('testUpdateFile')]
    public function testDeleteFile(): void
    {
        $response = $this->callEndpoint('delete', '/documents/images/origin/2021-01/filename.jpg');

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', $response->getContent());
    }

    #[Depends('testDeleteFile')]
    public function testDeleteNonExisting(): void
    {
        $response = $this->callEndpoint('delete', '/documents/images/origin/2021-01/filename.jpg');

        self::assertSame(404, $response->getStatusCode());
        self::assertSame('{"title":"File not exists","status":404}', $response->getContent());
    }

    private static function getInvalidJson(): string
    {
        return '{"name":"filename.jpg","date":"2021-01","file":[137,80,78,71,13,10,26,10,0,0,0,13,73,72,68,82,0,0,0,8,0,0,0,8,1,3,0,0,0,254,193,44,200,0,0,0,6,80,76,84,69,255,255,255,191,191,191,163,67,118,57,0,0,0,14,73,68,65,84,8,215,99,248,0,133,252,16,8,0,46,0,3,253,163,105,110,209,0,0,0,0,73,69,78,68,174,66,96,130]}';
    }

    private static function getTestJson(): string
    {
        return '{"name":"filename.jpg","date":"2021-01-02","file":[137,80,78,71,13,10,26,10,0,0,0,13,73,72,68,82,0,0,0,8,0,0,0,8,1,3,0,0,0,254,193,44,200,0,0,0,6,80,76,84,69,255,255,255,191,191,191,163,67,118,57,0,0,0,14,73,68,65,84,8,215,99,248,0,133,252,16,8,0,46,0,3,253,163,105,110,209,0,0,0,0,73,69,78,68,174,66,96,130]}';
    }
}
