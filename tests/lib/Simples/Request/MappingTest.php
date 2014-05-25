<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_MappingTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->client = new Simples_Transport_Http(array(
			'host' => ES_HOST,
			'index' => 'music',
			'type' => 'composers',
			'log' => true
		));
		$this->client->createIndex()->execute() ;
	}

	public function tearDown() {
		$this->client->deleteIndex()->execute() ;
	}

	public function testPath() {
		$request = $this->client->mapping() ;
		$this->assertEquals('/music/composers/_mapping/', (string) $request->path()) ;
		
		$request = $this->client->mapping(null, array('ignore_conflicts' => true)) ;
		$this->assertEquals('/music/composers/_mapping/?ignore_conflicts=1', (string) $request->path()) ;
	}

	public function testMethod() {
		$request = $this->client->mapping() ;
		$this->assertEquals(Simples_Request::GET, $request->method()) ;
		
		$request = $this->client->mapping(array(
			'Object' => array(
				'properties' => array(
					'field' => 'type'
				)
			)
		)) ;
		$this->assertEquals(Simples_Request::PUT, $request->method()) ;
	}
	
	public function testBody() {
		$json = '{"Object":{"properties":{"field":"type"}}}' ;
		$array = json_decode($json, true) ;
		
		// Works with an array or directly with the source
		$request = $this->client->mapping($json) ;
		$this->assertEquals($array, $request->to('array')) ;
		$this->assertEquals($json, $request->to('json')) ;
		
		$request = $this->client->mapping($array) ;
		$this->assertEquals($array, $request->to('array')) ;
		$this->assertEquals($json, $request->to('json')) ;
	}
	
	public function testRealcase() {
		$mapping = array(
			'composers' =>array(
				'properties' => array(
					'Composer' => array(
						'properties' => array(
							'name' => array('type' => 'string')
						)
					)
				)
			)
		);
		$request = $this->client->mapping($mapping) ;
		$response = $request->execute() ;
		$this->assertEquals(200, $response->http->http_code) ;
		
		$response = $this->client->mapping()->execute() ;
		$this->assertEquals($mapping, $response->body->music->mappings->to('array')) ;
	}
}
