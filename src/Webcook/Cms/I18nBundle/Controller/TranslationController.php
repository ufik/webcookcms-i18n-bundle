<?php

/**
 * This file is part of Webcook i18n bundle.
 *
 * See LICENSE file in the root of the bundle. Webcook
 */

namespace Webcook\Cms\I18nBundle\Controller;

use Webcook\Cms\CoreBundle\Base\BaseRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webcook\Cms\I18nBundle\Entity\Translation;
use Webcook\Cms\I18nBundle\Form\Type\TranslationType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Webcook\Cms\SecurityBundle\Authorization\Voter\WebcookCmsVoter;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Doctrine\DBAL\LockMode;

/**
 * Translation REST API controller.
 */
class TranslationController extends BaseRestController
{
    /**
     * Get all Translations.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Return collection of Translations.",
     * )
     * @Get(options={"i18n"=false})
     */
    public function getTranslationsAction()
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_VIEW);

        $translations = $this->getEntityManager()->getRepository('Webcook\Cms\I18nBundle\Entity\Translation')->findAll();
        $view = $this->view($translations, 200);

        return $this->handleView($view);
    }

    /**
     * Get single Translation.
     *
     * @param int $id Id of the desired Translation.
     *
     * @ApiDoc(
     *  description="Return single Translation.",
     *  parameters={
     *      {"name"="TranslationId", "dataType"="integer", "required"=true, "description"="Translation id."}
     *  }
     * )
     * @Get(options={"i18n"=false})
     */
    public function getTranslationAction($id)
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_VIEW);

        $translation = $this->getTranslationById($id);
        $view = $this->view($translation, 200);

        return $this->handleView($view);
    }

    /**
     * Save new Translation.
     *
     * @ApiDoc(
     *  description="Create a new Translation.",
     *  input="Webcook\Cms\I18nBundle\Form\Type\TranslationType",
     *  output="Webcook\Cms\I18nBundle\Entity\Translation",
     * )
     * @Post(options={"i18n"=false})
     */
    public function postTranslationsAction()
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_INSERT);

        $response = $this->processTranslationForm(new Translation(), 'POST');

        if ($response instanceof Translation) {
            $statusCode = 200;
            $message = 'Translation has been added.';
        } else {
            $statusCode = 400;
            $message = 'Error while adding new Translation.';
        }

        $view = $this->getViewWithMessage($response, $statusCode, $message);

        return $this->handleView($view);
    }

    /**
     * Update Translation.
     *
     * @param int $id Id of the desired Translation.
     *
     * @ApiDoc(
     *  description="Update existing Translation.",
     *  input="Webcook\Cms\I18nBundle\Form\Type\TranslationType",
     *  output="Webcook\Cms\I18nBundle\Entity\Translation"
     * )
     * @Put(options={"i18n"=false})
     */
    public function putTranslationAction($id)
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_EDIT);

        try {
            $translation = $this->getTranslationById($id, $this->getLockVersion((string) new Translation()));
        } catch (NotFoundHttpException $e) {
            $translation = new Translation();
        }

        $response = $this->processTranslationForm($translation, 'PUT');

        if ($response instanceof Translation) {
            $statusCode = 204;
            $message = 'Translation has been updated.';
        } else {
            $statusCode = 400;
            $message = 'Error while updating Translation.';
        }

        $view = $this->getViewWithMessage($response, $statusCode, $message);

        return $this->handleView($view);
    }

    /**
     * Delete Translation.
     *
     * @param int $id Id of the desired Translation.
     *
     * @ApiDoc(
     *  description="Delete Translation.",
     *  parameters={
     *     {"name"="TranslationId", "dataType"="integer", "required"=true, "description"="Translation id."}
     *  }
     * )
     * @Delete(options={"i18n"=false})
     */
    public function deleteTranslationAction($id)
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_DELETE);

        $translation = $this->getTranslationById($id);

        $this->getEntityManager()->remove($translation);
        $this->getEntityManager()->flush();

        $view = $this->getViewWithMessage(array(), 200, 'Translation has been deleted.');

        return $this->handleView($view);
    }

    /**
     * Return form if is not valid, otherwise process form and return Translation object.
     *
     * @param Translation   $translation
     * @param string     $method Method of request
     *
     * @return \Symfony\Component\Form\Form [description]
     */
    private function processTranslationForm(Translation $translation, String $method = 'POST')
    {
        $form = $this->createForm(TranslationType::class, $translation);
        $form = $this->formSubmit($form, $method);
        if ($form->isValid()) {
            $translation = $form->getData();

            if ($translation instanceof Translation) {
                $this->getEntityManager()->persist($translation);
            }

            $this->getEntityManager()->flush();

            return $translation;
        }

        return $form;
    }

    /**
     * Get Translation by id.
     *
     * @param int $id [description]
     *
     * @return Translation
     *
     * @throws NotFoundHttpException If Translation doesn't exist
     */
    private function getTranslationById($id, $expectedVersion = null)
    {
        if ($expectedVersion) {
            $translation = $this->getEntityManager()->getRepository('Webcook\Cms\I18nBundle\Entity\Translation')->find($id, LockMode::OPTIMISTIC, $expectedVersion);
        } else {
            $translation = $this->getEntityManager()->getRepository('Webcook\Cms\I18nBundle\Entity\Translation')->find($id);
        }

        if (!$translation instanceof Translation) {
            throw new NotFoundHttpException('Translation not found.');
        }

        $this->saveLockVersion($translation);

        return $translation;
    }
}
