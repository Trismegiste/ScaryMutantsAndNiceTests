<?php

/*
 * Smant
 */

namespace Trismegiste\Smant\Visitor;

/**
 * MadScientist is a visitor which mutates caught classes
 */
class MadScientist extends \PHPParser_NodeVisitorAbstract
{

    protected $filename;

    public function __construct($fch)
    {
        $this->filename = $fch;
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        switch ($node->getType()) {

            case 'Scalar_DirConst':
                return new \PHPParser_Node_Scalar_String(dirname($this->filename));
                break;

            case 'Expr_ConstFetch':
                if ($node->name == 'true') {
                    return new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name('false'));
                }

                break;
        }
    }

}