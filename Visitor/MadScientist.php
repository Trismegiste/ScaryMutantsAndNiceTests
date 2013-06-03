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

            case 'Scalar_FileConst':
                return new \PHPParser_Node_Scalar_String($this->filename);
                break;

            case 'Scalar_DirConst':
                return new \PHPParser_Node_Scalar_String(dirname($this->filename));
                break;

            // mutation true |---> false and false |---> true
            case 'Expr_ConstFetch':
                $choice = array(-1 => 'false', 1 => 'true');
                if (false !== $key = array_search((string) $node->name, $choice)) {
                    $node->name = new \PHPParser_Node_Name($choice[-$key]);
                    return $node;
                }
                break;
        }
    }

}