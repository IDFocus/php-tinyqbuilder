<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use \IDFocus\TinyQ\Builder;

$builder = new Builder();

$builder->not()
        ->equal('name', 'henry.aaron')
        ->or()
        ->lparen()
        ->greaterOrEqual('age', 18)
        ->and()
        ->LessOrEqual('age', 65)
        ->rparen();
echo $builder->build(); // .tname.ehenry..aaron.o.sage.h18.aage.m65.f