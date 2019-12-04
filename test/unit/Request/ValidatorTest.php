<?php

namespace Test\Unit;

use Emartech\Request\Validator;
use Emartech\TestHelper\BaseTestCase;
use GuzzleHttp\Psr7\ServerRequest;
use InvalidArgumentException;

class ValidatorTest extends BaseTestCase
{
    /**
     * @test
     */
    public function validatePostData_RequiredFieldMissing_ExceptionThrown()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new ServerRequest('POST', '/');
        $validator = new Validator();
        $validator->validatePostData($request, ['entryId']);
    }

    /**
     * @test
     */
    public function validatePostData_RequiredFieldsPresent_PostDataReturned()
    {
        $request = new ServerRequest('POST', '/', [], '{"entryId" : 1}');
        $validator = new Validator();
        $this->assertEquals(['entryId' => 1], $validator->validatePostData($request, ['entryId']));
    }

    /**
     * @test
     */
    public function validatePostData_NoRequiredFieldsAndEmptyBody_EmptyArrayReturned()
    {
        $request = new ServerRequest('POST', '/');
        $validator = new Validator();
        $this->assertEquals([], $validator->validatePostData($request, []));
    }
}
