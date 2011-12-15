<?php

/**
 * Search. Oh yea, here it is.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search extends Simples_Request {
	
	/**
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::POST,
		'path' => '_search',
		'magic' => 'query',
	) ;
	
	
	/**
	 * Default body values.
	 * 
	 * @var array
	 */
	protected $_body = array(
		'index' => null,
		'type' => null,
		'query' => null,
		'filter' => null,
		'facets' => null,
		'from' => null,
		'size' => null,
		'sort' => null,
		'highlight' => null,
		'fields' => null,
		'script_fields' => null,
		'explain' => false,
		'version' => null,
		'min_score' => null
	);
	
	/**
	 * Current subobject working.
	 * 
	 * @var string
	 */
	protected $_current = 'query' ;
	
	public function __construct($body = null, $options = null, Simples_Transport $transport = null) {
		$this->_query = new Simples_Request_Search_Query(null, $this) ;
		
		if (isset($body['query']) && is_string($body['query'])) {
			$this->_query->add($body['query']) ;
			unset($body['query']) ;
		}
		
		parent::__construct($body, $options, $transport);
	}
	
	/**
	 * Body without null values.
	 * 
	 * @param array $body
	 * @return type 
	 */
	public function body(array $body = null) {
		if (isset($body)) {
			return parent::body($body) ;
		}
		
		$body = parent::body() ;
		
		if (empty($body['query'])) {
			$body['query'] = $this->_query->to('array') ;
		}
		
		$body = array_filter($body) ;
		
		return $body ;
	}
	
	/**
	 * Query getter/setter.
	 * 
	 * @param mixed		$query			Setter : Query definition.
	 * @return \Simples_Request_Search	This instance
	 */
	public function query($query = null) {
		// Save current subobject
		$this->_current = 'query' ;
		
		if (isset($query)) {
			$this->_query->add($query) ;
		}
		return $this ;
	}
	
	/**
	 * Add multiples field queries one time. It's a simplified call wich permit to give this kind of array :
	 * $request->queries(array(
	 *		'field' => 'value',
	 *		'other_field' => array('value 1', 'value 2')
	 * ));
	 * 
	 * @param array $queries			List of criteries. Field name in key, search in value.
	 * @return \Simples_Request_Search	This instance.
	 */
	public function queries(array $queries) {
		foreach($queries as $in => $match) {
			$this->_query->add(array('query' => $match, 'in' => $in)) ;
		}
		return $this ;
	}
	
	/**
	 * Set the from param.
	 * 
	 * @param int	$from		From value
	 * @return \Simples_Request_Search 
	 */
	public function from($from) {
		$this->_body['from'] = $from ;
		return $this ;
	}
	
	/**
	 * Set the size.
	 * 
	 * @param int	$size		Size value
	 * @return \Simples_Request_Search 
	 */
	public function size($size) {
		$this->_body['size'] = $size;
		return $this ;
	}
	
	/**
	 * Set the sort param.
	 * 
	 * @param string	$sort	Sort value
	 * @return \Simples_Request_Search 
	 */
	public function sort($sort) {
		$this->_body['sort'] = $sort;
		return $this ;
	}
	
	/**
	 * Magic call : chain with subobjects.
	 * 
	 * @param string	$name		Method name
	 * @param array		$args		Arguments
	 * @return \Simples_Request_Search 
	 */
	public function __call($name, $args) {
		$object = '_' . $this->_current ;
		call_user_func_array(array($this->{$object}, $name), $args) ;
		return $this ;
	}
}