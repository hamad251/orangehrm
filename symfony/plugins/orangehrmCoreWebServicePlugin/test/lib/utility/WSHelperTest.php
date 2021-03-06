<?php

/**
 * Test class for WSHelper.
 * Generated by PHPUnit on 2012-06-13 at 09:47:16.
 * @group WebServices
 */
class WSHelperTest extends PHPUnit_Framework_TestCase {

    /**
     * @var WSHelper
     */
    protected $helper;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->helper = new WSHelper();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers WSHelper::getWSUtilityService 
     */
    public function testGetWebServiceUtilityService() {
        $service = $this->helper->getWSUtilityService();
        $this->assertTrue($service instanceof WSUtilityService);

        $newService = new WSUtilityService();
        $this->helper->setWSUtilityService($newService);
        $service = $this->helper->getWSUtilityService();
        $this->assertEquals($newService, $service);
    }

    /**
     * @covers WSHelper::setWSUtilityService 
     */
    public function testSetWebServiceUtilityService() {
        $newService = new WSUtilityService();
        $this->helper->setWSUtilityService($newService);
        $service = $this->helper->getWSUtilityService();
        $this->assertEquals($newService, $service);
    }

    /**
     * @covers WSHelper::extractParameters
     */
    public function testExtractParameters() {
        $requestMock = $this->getMock('sfWebRequest', array('getMethod', 'getHttpHeader', 'getRequestParameters', 'getContentType'), 
            array(new sfEventDispatcher()));
        $requestMock->expects($this->once())
                ->method('getMethod')
                ->will($this->returnValue('GET'));
        $requestMock->expects($this->once())
                ->method('getContentType')
                ->will($this->returnValue('text/plain'));

        $requestMock->expects($this->once())
                ->method('getHttpHeader')
                ->with('ohrm_ws_method_parameters')
                ->will($this->returnValue(json_encode(array('page' => 2, 'limit' => 50))
                        ));
        $requestMock->expects($this->once())
                ->method('getRequestParameters')
                ->will($this->returnValue(array(
                            'module' => 'api',
                            'action' => 'wsCall',
                            'ws_method' => 'getEmployeeList',
                            '_sf_route' => null,
                        )));

        $resultWSRequestParamObj = $this->helper->extractParameters($requestMock);
        $this->assertTrue($resultWSRequestParamObj instanceof WSRequestParameters);
        $this->assertEquals('GET', $resultWSRequestParamObj->getRequestMethod());
        $this->assertEquals('getEmployeeList', $resultWSRequestParamObj->getMethod());
        $this->assertEquals(array('page' => 2, 'limit' => 50), $resultWSRequestParamObj->getParameters());
    }

    /**
     * @covers WSHelper::extractParameters
     * @expectedException WebServiceException
     */
    public function testextractParameters_InvalidAuthParameterHeaders() {
        $requestMock = $this->getMock('sfWebRequest', array('getMethod', 'getHttpHeader', 'getRequestParameters'), array(new sfEventDispatcher()));
        $requestMock->expects($this->once())
                ->method('getMethod')
                ->will($this->returnValue('GET'));
        $requestMock->expects($this->any())
                ->method('getHttpHeader')
                ->will($this->onConsecutiveCalls(
                                'bad json', json_encode(array('page' => 2, 'limit' => 50))
                        ));
        $requestMock->expects($this->once())
                ->method('getRequestParameters')
                ->will($this->returnValue(array(
                            'module' => 'api',
                            'action' => 'wsCall',
                            'getEmployeeList' => '1',
                            '_sf_route' => null,
                        )));

        $this->helper->extractParameters($requestMock);
    }

    /**
     * @covers WSHelper::extractParameters
     * @expectedException WebServiceException
     */
    public function testextractParameters_WithEmptyModuleAndMethod() {
        $requestMock = $this->getMock('sfWebRequest', array('getMethod', 'getHttpHeader', 'getRequestParameters'), array(new sfEventDispatcher()));
        $requestMock->expects($this->once())
                ->method('getMethod')
                ->will($this->returnValue('GET'));
        $requestMock->expects($this->any())
                ->method('getHttpHeader')
                ->will($this->onConsecutiveCalls(
                        'bad json', json_encode(array('page' => 2, 'limit' => 50))
                        ));
        $requestMock->expects($this->once())
                ->method('getRequestParameters')
                ->will($this->returnValue(array(
                            'module' => 'api',
                            'action' => 'wsCall',
                            '_sf_route' => null,
                        )));

        $this->helper->extractParameters($requestMock);
    }

    /**
     * @covers WSHelper::formatResult
     */
    public function testFormatResult() {
        $result = new stdClass();
        $result->name = 'Test';
        $result->score = 100;

        $expcetedString = json_encode($result);

        $wsUtilityServiceMock = $this->getMock('WSUtilityService', array('format'));
        $wsUtilityServiceMock->expects($this->once())
                ->method('format')
                ->will($this->returnValue(json_encode($result)));
        $this->helper->setWSUtilityService($wsUtilityServiceMock);

        $this->assertEquals($expcetedString, $this->helper->formatResult($result, WSHelper::FORMAT_JSON));
    }

}
