<?php
namespace Eltharin\AjaxResponserBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Eltharin\AjaxResponserBundle\Annotations\AjaxCallOrNot;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class AjaxResponseConverterEventSubscriber implements EventSubscriberInterface
{
	private $exception = null;
	public function __construct(private HttpKernel $kernel, private ClassMetadataFactoryInterface $classMetadataFactory, private EntityManagerInterface $entityManager)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			ResponseEvent::class => 'onKernelResponse',
			ExceptionEvent::class => 'onKernelException',
		];
	}

	public function onKernelResponse(ResponseEvent $event)
	{
		if(!$event->isMainRequest() || !$event->getRequest()->attributes->has('_controller'))
		{
			return;
		}

		list($controller, $method) = explode('::', $event->getRequest()->attributes->get('_controller'));

		try {
			$controllerMetaData = $this->classMetadataFactory->getMetadataFor($controller);
		}
		catch (InvalidArgumentException $e)
		{
			return;
		}

		$response = $event->getResponse();

		if(!empty($attribute = $controllerMetaData->getReflectionClass()->getMethod($method)->getAttributes(AjaxCallOrNot::class)))
		{
			$attribute = $attribute[0];

			if( $event->getRequest()->isXmlHttpRequest())
			{
				$data = [
					'statusCode' => $response->getStatusCode(),
					'content' => $response->getContent()
				];

				$response->headers->set('Content-Type', 'application/json');
				$response->headers->set('X-Response-Type', 'AjaxOrNotResponse');

				$data['msgs'] = $event->getRequest()->getSession()->getFlashBag()->all();
				$data['header'] = $event->getRequest()->headers->get('x-with-redirect');

				if(substr($response->getStatusCode(),0,1) == '3')
				{
					if(($event->getRequest()->headers->get('x-redirect-type') == null || $event->getRequest()->headers->get('x-redirect-type') == "forward"))
					{
						$data['redirectUrl'] = $response->headers->get('Location');
						$response->headers->remove('Location');

						$response->setStatusCode(200);

						if(!array_key_exists('getRedirectContent', $attribute->getArguments()) || $attribute->getArguments()['getRedirectContent'] == true)
						{
							$data['content'] = $this->getRedirectContent($event);
						}
					}
				}
				elseif(substr($response->getStatusCode(),0,1) == '4' || substr($response->getStatusCode(),0,1) == '5')
				{
					if($this->exception !== null)
					{
						$data['content'] = $this->exception->getMessage();
						$data['msgs']['danger'][] = $this->exception->getMessage();
					}
					else
					{

						$data['errorForm'] = true;
					}
				}

				$response->setContent(json_encode($data));
			}
		}
	}

	public function onKernelException(ExceptionEvent $event)
	{
		$this->exception = $event->getThrowable();
	}

	public function getRedirectContent(ResponseEvent $event)
	{
		$this->entityManager->clear();

		$subRequest = Request::create(
			$event->getResponse()->getTargetUrl(),
			'GET',
			[],
			$event->getRequest()->cookies->all(),
			[],
			$event->getRequest()->server->all()
		);

		$subRequest->setSession($event->getRequest()->getSession());

		return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST)->getContent();
	}
}
