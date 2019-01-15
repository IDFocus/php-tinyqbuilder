<?php
/**
 * A helper class to allow easy programmatic construction of TinyQ expressions. 
 * @author IDFocus B.V. <nivo@idfoucs.nl>
 */

namespace IDFocus\TinyQ;

/**
 * A helper class to allow easy programmatic construction of TinyQ expressions. 
 * @method Builder and() Add the TinyQ equivalent of the "AND" operator to the expression.
 * @method Builder or() Add the TinyQ equivalent of the "OR" operator to the expression.
 */
class Builder{
	/**
	 * Query string currently being created.
	 * @var string $queryString 
	 */
	protected $queryString = '';

	const GREATER                       = ".g";
	const GREATER_OR_EQUAL              = ".h";
	const LESS                          = ".l";
	const LESS_OR_EQUAL                 = ".m";
	const NOT_EQUAL                     = ".n";
	const NOT                           = ".t";
	const _AND                          = ".a";
	const _OR                           = ".o";
	const LIKE                          = ".k";
	const LPAREN                        = ".s";
	const RPAREN                        = ".f";
	const EQUAL                         = ".e";
	const ESCAPE_DOT                    = "..";
	const ESCAPE_LIKE_WILDCARD_ONE      = "_";
	const ESCAPE_LIKE_WILDCARD_MULTIPLE = "-";
	const LIKE_WILDCARD_ONE             = "._";
	const LIKE_WILDCARD_MULTIPLE        = ".-";


	/**
	 * Construct an instance.
	 * @example examples/basic.php 5 Example of creating a Buildquery
	 */	
	public function __construct(){}

	/**
	 * Escape key and value query parts.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @param string $operator TinyQ operator
	 * @return string
	 */
	protected function createEscapedString($key, $value, $operator){
		return $this->escape($key) . $operator . $this->escape($value);
	}

	/**
	 * Add the TinyQ equivalent of the ">" operator to the expression.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @return Builder
	 */
	public function greater($key, $value){
		$this->queryString .= $this->createEscapedString($key, $value, self::GREATER);
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "<=" operator to the expression.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @return Builder
	 */
	public function greaterOrEqual($key, $value){
		$this->queryString .= $this->createEscapedString($key, $value, self::GREATER_OR_EQUAL);
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "<" operator to the expression.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @return Builder
	 */
	public function less($key, $value){
		$this->queryString .= $this->createEscapedString($key, $value, self::LESS);
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "<=" operator to the expression.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @return Builder
	 */
	public function lessOrEqual($key, $value){
		$this->queryString .= $this->createEscapedString($key, $value, self::LESS_OR_EQUAL);
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "!=" operator to the expression.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @return Builder
	 */
	public function notEqual($key, $value){
		$this->queryString .= $this->createEscapedString($key, $value, self::NOT_EQUAL);
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "NOT" operator to the expression.
	 * @return Builder
	 */
	public function not(){
		$this->queryString .= self::NOT;
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "AND" operator to the expression. Method for reserved `and` keyword.
	 * @return Builder
	 */
	protected function _and(){
		$this->queryString .= self::_AND;
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "OR" operator to the expression. Method for reserved `or` keyword.
	 * @return Builder
	 */	
	protected function _or(){
		$this->queryString .= self::_OR;
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "LIKE" operator to the expression.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @return Builder
	 */
	public function like($key, $value){
		$this->queryString .= $this->createEscapedString($key, $value, self::LIKE);
		return $this;
	}

	/**
	 * Append the equivalent of a left parenthesis (the left grouping operator);
	 * @return Builder
	 */
	public function lparen(){
		$this->queryString .= self::LPAREN;
		return $this;
	}

	/**
	 * Append the equivalent of a right parenthesis (the right grouping operator);
	 * @return Builder
	 */
	public function rparen(){
		$this->queryString .= self::RPAREN;
		return $this;
	}

	/**
	 * Add the TinyQ equivalent of the "==" operator to the expression.
	 * @param string $key Property to search
	 * @param mixed $value Searchterm
	 * @return Builder
	 */
	public function equal($key, $value){
		$this->queryString .= $this->createEscapedString($key, $value, self::EQUAL);
		return $this;
	}

	/**
	 * Reset the instance so another expression can be built.
	 * @return Builder
	 */
	public function reset(){
		$this->queryString = '';
		return $this;
	}

	/**
	 * Return the TinyQ language expression.
	 * @return string
	 */
	public function build(){
		return $this->queryString;
	}

	/**
	 * Overloading to be able to use `and`, `or` methods.
	 * @ignore
	 * @param string $method Magic method to call
	 * @param mixed[] $args Arguments to pass to the magic method
	 */
	public function __call($method, $args){
		if($method == 'and'){
			return call_user_func_array(array($this,"_and"), $args);
		}elseif($method == 'or'){
			return call_user_func_array(array($this,"_or"), $args);
		}else{
			throw new \BadMethodCallException("Method {$method} doesn't exists");
		}
	}

	/**
	 * Escape the query paramaters.
	 * @param string $string String to be escaped
	 * @return string
	 */
	protected function escape($string){
		$string = str_replace('.', self::ESCAPE_DOT, $string);
		$string = str_replace('.' . self::LIKE_WILDCARD_MULTIPLE, self::LIKE_WILDCARD_MULTIPLE, $string);
		$string = str_replace('.' . self::LIKE_WILDCARD_ONE, self::LIKE_WILDCARD_ONE, $string);
		return $string;
	}

	/**
	 * Escape queries to be used in LIKE statements.
	 * @param string $string String to be escaped
	 * @return string
	 */
	public function escapeLike($string){
		$string = str_replace(self::ESCAPE_LIKE_WILDCARD_MULTIPLE, self::LIKE_WILDCARD_MULTIPLE, $string);
		$string = str_replace(self::ESCAPE_LIKE_WILDCARD_ONE, self::LIKE_WILDCARD_ONE, $string);
		return $string;
	}

}