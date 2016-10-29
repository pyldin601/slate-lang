<?php

namespace PeacefulBit\Packet\Visitors;

use PeacefulBit\Packet\Exception\RuntimeException;
use PeacefulBit\Packet\Nodes;
use PeacefulBit\Packet\Context\Context;

class NodeCalculatorVisitor implements NodeVisitor
{
    use NodeDispatchingTrait;

    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function visitConstantNode(Nodes\ConstantNode $node)
    {
        $combined = $node->getCombined();
        $keys = array_keys($combined);

        array_walk($keys, function ($key) use ($node, $combined) {
            $visitor = $this->getVisitor($combined[$key]);
            $value = $visitor($combined[$key]);
            $this->context->set($key, $value);
        });

        return null;
    }

    public function visitFunctionNode(Nodes\FunctionNode $node)
    {
        $this->context->set($node->getName(), $node);

        return null;
    }

    /*
     * Warning! Causes recursion!
     */
    public function visitInvokeNode(Nodes\InvokeNode $node)
    {
        $result = $node;

        while ($result instanceof Nodes\InvokeNode) {

            $callable = $this->getFunction($result);

            if ($callable instanceof Nodes\NativeNode) {
                $result = call_user_func($callable->getCallable(), $this, $result->getArguments());
            } elseif ($callable instanceof Nodes\NativeMacroNode) {
                $result = $this->visit(call_user_func($callable->getCallable(), $this, $result->getArguments()));
            } elseif ($callable instanceof Nodes\LambdaNode) {
                $result = $this->callLambdaNode($callable, $result->getArguments());
            } else {
                throw new \RuntimeException("Symbol is not callable");
            }

        }

        return $result;
    }

    private function getFunction(Nodes\InvokeNode $node)
    {
        $function = $node->getFunction();

        if ($function instanceof Nodes\SymbolNode) {
            $name = $function->getName();
            if (!$this->context->has($name)) {
                throw new \RuntimeException("Symbol \"$name\" is not defined");
            }
            return $this->context->get($name);
        }

        if ($function instanceof Nodes\InvokeNode) {
            return $this->visitInvokeNode($function);
        }

        throw new \RuntimeException("Invalid function");
    }

    private function callLambdaNode(Nodes\LambdaNode $node, $args)
    {
        $argNames = $node->getArguments();

        if (sizeof($argNames) > sizeof($args)) {
            throw new \RuntimeException("Number of arguments mismatch");
        }

        $combined = array_combine($argNames, $this->visitListOfNodes($args));
        $childContext = $this->context->newContext($combined);
        $childVisitor = new static($childContext);

        $body = $node->getBody();
        $visitor = $childVisitor->getVisitor($body);

        return $visitor($body);
    }

    private function visitListOfNodes(array $listOfNodes)
    {
        return array_map([$this, 'visit'], $listOfNodes);
    }

    public function visitSequenceNode(Nodes\SequenceNode $node)
    {
        return array_reduce($node->getNodes(), function ($prev, $next) {
            $visitor = $this->getVisitor($next);
            return $visitor($next);
        });
    }

    public function visitStringNode(Nodes\StringNode $node)
    {
        return $node->getValue();
    }

    public function visitSymbolNode(Nodes\SymbolNode $node)
    {
        $name = $node->getName();

        if (is_numeric($name)) {
            return (false !== strpos($name, '.'))
                ? floatval($name)
                : intval($name);
        }

        if (!$this->context->has($name)) {
            throw new RuntimeException("Symbol \"$name\" not defined");
        }

        return $this->context->get($name);
    }

    public function visitNativeNode(Nodes\NativeNode $node)
    {
        // Native code could not came from source
    }

    public function valueOf($node)
    {
        if (is_null($node)) {
            return null;
        }
        if (is_array($node)) {
            return json_encode($node);
        }
        if (is_scalar($node)) {
            return $node;
        }
        if ($node instanceof Nodes\StringNode) {
            return $node->getValue();
        }
        if ($node instanceof Nodes\LambdaNode) {
            return '[function]';
        }
        $visitor = $this->getVisitor($node);
        return $this->valueOf($visitor($node));
    }

    public function visitLambdaNode(Nodes\LambdaNode $node)
    {
        return $node;
    }
}
