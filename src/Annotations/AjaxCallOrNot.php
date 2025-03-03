<?php

namespace Eltharin\AjaxResponserBundle\Annotations;

use Attribute;
use Doctrine\ORM\Mapping\MappingAttribute;

#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class AjaxCallOrNot implements MappingAttribute
{
	public function __construct(
		private bool $getRedirectContent = true,
		private mixed $selectorOnAjax = null,
	) {
	}
}
