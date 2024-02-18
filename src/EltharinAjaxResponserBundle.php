<?php

namespace Eltharin\AjaxResponserBundle;

use Doctrine\ORM\EntityManagerInterface;
use Eltharin\AjaxResponserBundle\EventListener\AjaxResponseConverterEventSubscriber;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class EltharinAjaxResponserBundle extends AbstractBundle
{
	public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$container->services()
			->set(AjaxResponseConverterEventSubscriber::class)
			->args([
				service('http_kernel'),
				service(ClassMetadataFactoryInterface::class),
				service(EntityManagerInterface::class),
			])
			->tag('kernel.event_subscriber')
		;
	}
}
