<?php

/*
 * Smant
 */

namespace Trismegiste\Smant\Visitor;

/**
 * MadScientist is a visitor which mutates caught classes
 */
class MadScientist extends PetriDish
{

    protected function mutateNode(\PHPParser_Node $node)
    {

        switch ($node->getType()) {
            // mutation true |---> false and false |---> true
            case 'Expr_ConstFetch':
                $choice = array(-1 => 'false', 1 => 'true');
                if (false !== $key = array_search((string) $node->name, $choice)) {
                    $node->name = new \PHPParser_Node_Name($choice[-$key]);
                    return $node;
                }
                break;

            // add +/- 1 to integer
            case 'Scalar_LNumber':
                $node->value += 2 * rand(0, 1) - 1;
                return $node;
                break;

            // add +/- 10% to double
            case 'Scalar_DNumber':
                $node->value *= (9 + 2 * rand(0, 1)) / 10.0;
                break;
        }
    }

}