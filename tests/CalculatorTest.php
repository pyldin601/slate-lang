<?php

namespace tests;

class CalculatorTest extends AbstractEvaluatorTestCase
{
    public function testNativeCall()
    {
        $result = $this->evaluate("(? 1 2)");

        $this->assertEquals('1 ? 2', $result);
    }

    public function testStringCall()
    {
        $result = $this->evaluate('"foo"');
        $this->assertEquals('foo', $result);
    }

    public function testConstantDefine()
    {
        $result = $this->evaluate('(def foo "bar") foo');
        $this->assertEquals('bar', $result);
    }

    public function testFunctionDefine()
    {
        $result = $this->evaluate('(def (foo) "bar") (foo)');
        $this->assertEquals('bar', $result);
    }

    public function testFunctionAsArgument()
    {
        $result = $this->evaluate('(def (foo) "bar") (def (baz) foo) ((baz))');
        $this->assertEquals('bar', $result);
    }

    public function testFunctionalComposition()
    {
        $result = $this->evaluate('
            (def name "Foo")
            (def (greet name) (? "Hello, " name))
            (greet name)
        ');
        $this->assertEquals('Hello,  ? Foo', $result);
    }

    public function testLambdaExpression()
    {
        $result = $this->evaluate('
            (def func (lambda (x) (? x 2)))
            (func 8)
        ');
        $this->assertEquals('8 ? 2', $result);
    }

    public function testArgumentsScopes()
    {
        $result = $this->evaluate('
            (def a "foo")
            (def (func a) (? a a))
            (func "bar")
        ');
        $this->assertEquals('bar ? bar', $result);
    }
}
