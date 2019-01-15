<?php

use \IDFocus\TinyQ\Builder;

namespace IDFocus\TinyQ;

class BuilderTest extends \PHPUnit_Framework_TestCase{

	public function testCanBuild(){
		$builder = new Builder();
		$this->assertEquals(
			"startdate.g{{DATETIMESTAMP}}.azoekterm.k{{STRING}}", 
			$builder->greater("startdate", "{{DATETIMESTAMP}}")->and()->like("zoekterm", "{{STRING}}")->build()
		);

		$builder->reset();
		$this->assertEquals(
			"startdate.g{{DATETIMESTAMP}}.o.szoekterm.k{{STRING}}.aloglevel.e{{LOGLEVEL}}.f", 
			$builder->greater("startdate", "{{DATETIMESTAMP}}")->or()->lparen()->like("zoekterm", "{{STRING}}")->and()->equal("loglevel", "{{LOGLEVEL}}")->rparen()->build()
		);
	}

	public function testImplementsAnd(){
		$builder = new Builder();
		$builder->and();
		$this->assertEquals('.a', $builder->build());
	}

	public function testImplementsEqual(){
		$builder = new Builder();
		$builder->equal('name', 'test');
		$this->assertEquals('name.etest', $builder->build());
	}

	public function testImplementsGreater(){
		$builder = new Builder();
		$builder->greater('name', 15);
		$this->assertEquals('name.g15', $builder->build());
	}

	public function testImplementsGreaterOrEqual(){
		$builder = new Builder();
		$builder->greaterOrEqual('name', 15);
		$this->assertEquals('name.h15', $builder->build());
	}

	public function testImplementsLess(){
		$builder = new Builder();
		$builder->less('name', 15);
		$this->assertEquals('name.l15', $builder->build());
	}

	public function testImplementsLessOrEqual(){
		$builder = new Builder();
		$builder->lessOrEqual('name', 15);
		$this->assertEquals('name.m15', $builder->build());
	}

	public function testImplementsLike(){
		$builder = new Builder();
		$builder->like('name', 'test');
		$this->assertEquals('name.ktest', $builder->build());
	}

	public function testImplementsLParen(){
		$builder = new Builder();
		$builder->lparen();
		$this->assertEquals('.s', $builder->build());
	}

	public function testImplementsNot(){
		$builder = new Builder();
		$builder->not();
		$this->assertEquals('.t', $builder->build());
	}

	public function testImplementsNotEqual(){
		$builder = new Builder();
		$builder->notEqual('name', 'test');
		$this->assertEquals('name.ntest', $builder->build());
	}

	public function testImplementsOr(){
		$builder = new Builder();
		$builder->or();
		$this->assertEquals('.o', $builder->build());
	}

	public function testImplementsReset(){
		$builder = new Builder();
		$builder->greater('x', 10);
		$this->assertNotEmpty($builder->build());
		$builder->reset();
		$this->assertEmpty($builder->build());
	}

	public function testImplementsRParen(){
		$builder = new Builder();
		$builder->rparen();
		$this->assertEquals('.f', $builder->build());
	}

	public function testCanEscapeDots(){
		$builder = new Builder();
		$builder->equal('name', 'henry.aaron');
		$this->assertEquals('name.ehenry..aaron', $builder->build());
	}

	public function testCanEscapeLikes(){
		$builder = new Builder();
		$builder->like('name', $builder->escapeLike('some_text') . '-');
		$this->assertEquals('name.ksome._text-', $builder->build());

		$builder = new Builder();

		$builder->like('name', $builder->escapeLike('_some_text-') . '-');
		$this->assertEquals('name.k._some._text.--', $builder->build());
	}

	public function testThrowOnIllegalMethod(){
		$builder = new Builder();
		$this->setExpectedException(
			'\BadMethodCallException'
		);
		$builder->illegalMethod();
	}
}