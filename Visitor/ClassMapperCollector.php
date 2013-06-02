<?php

/*
 * Scary Mutants and Nice Tests
 */

namespace Trismegiste\Smant\Visitor;

/**
 * ClassMapperCollector is a visitor which collects FQCN of classes
 */
class ClassMapperCollector extends \PHPParser_NodeVisitorAbstract
{

    protected $mapping;
    protected $currentNamespace;

    public function beforeTraverse(array $nodes)
    {
        $this->mapping = array();
    }

    public function enterNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_Namespace':
                $this->currentNamespace = $node->name;
                break;

            case 'Stmt_Class':
                $fqcn = clone $this->currentNamespace;
                $fqcn->append($node->name);
                $this->mapping[] = (string) $fqcn;
                break;
        }
    }

    public function getDeclaredClass()
    {
        return $this->mapping;
    }

}