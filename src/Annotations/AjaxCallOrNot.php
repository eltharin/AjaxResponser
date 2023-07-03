<?php

namespace Eltharin\AjaxResponserBundle\Annotations;

use Attribute;
use Doctrine\ORM\Mapping\MappingAttribute;

#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class AjaxCallOrNot implements MappingAttribute
{

}
