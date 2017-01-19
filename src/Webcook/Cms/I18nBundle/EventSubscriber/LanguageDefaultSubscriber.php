<?php

namespace Webcook\Cms\I18nBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Webcook\Cms\I18nBundle\Entity\Language;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LanguageDefaultSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [['setDefault', EventPriorities::POST_WRITE]],
        ];
    }

    public function setDefault(GetResponseForControllerResultEvent $event)
    {
        $language = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$language instanceof Language) {
            return;
        }

        if ($language->isDefault()) {
            if (Request::METHOD_POST === $method || Request::METHOD_PUT === $method) {
                $languages = $this->em->getRepository('Webcook\Cms\I18nBundle\Entity\Language')->findBy(array(
                    'default' => true
                ));

                foreach ($languages as $l) {
                    if ($l !== $language) {
                        $l->setDefault(false);
                    }
                }
            } else {
                $language = $this->em->getRepository('Webcook\Cms\I18nBundle\Entity\Language')->findAll()[0];

                $language->setDefault(true);
            }
        }

        $this->em->flush();
    }
}