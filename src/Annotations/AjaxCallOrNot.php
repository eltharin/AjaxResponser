<?php

namespace Eltharin\AjaxResponserBundle\Annotations;

use Attribute;
use Doctrine\ORM\Mapping\Annotation;

#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class AjaxCallOrNot implements Annotation
{

}
