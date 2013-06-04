<?php

/*
 * ScaryMutants
 */

namespace Trismegiste\Smant\Visitor;

/**
 * PetriDish is a visitor to simulate a class in petri dish with eval()'d
 *
 * For example, it replaces constants like __FILE__ and __DIR__ which fail when
 * a class is eval()'d instead of being loaded
 */
class PetriDish extends \PHPParser_NodeVisitorAbstract
{

    protected $filename;
    protected $dryRun;

    public function __construct($fch, $dry)
    {
        $this->filename = $fch;
        $this->dryRun = $dry;
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

            default:
                if (!$this->dryRun) {
                    return $this->mutateNode($node);
                }
                break;
        }
    }

    protected function mutateNode(\PHPParser_Node $node)
    {
        return null;
    }

}