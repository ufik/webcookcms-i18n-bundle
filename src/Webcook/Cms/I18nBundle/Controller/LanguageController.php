<?php

/**
 * This file is part of Webcook common bundle.
 *
 * See LICENSE file in the root of the bundle. Webcook
 */

namespace Webcook\Cms\I18nBundle\Controller;

use Webcook\Cms\CoreBundle\Base\BaseRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webcook\Cms\I18nBundle\Entity\Language;
use Webcook\Cms\I18nBundle\Form\Type\LanguageType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Webcook\Cms\SecurityBundle\Authorization\Voter\WebcookCmsVoter;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Doctrine\DBAL\LockMode;

/**
 * Language controller.
 */
class LanguageController extends BaseRestController
{
    /**
     * Get all Languages.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Return collection of Languages.",
     * )
     * @Get(options={"i18n"=false})
     */
    public function getLanguagesAction()
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_VIEW);

        $languages = $this->getEntityManager()->getRepository('Webcook\Cms\I18nBundle\Entity\Language')->findAll();
        $view = $this->view($languages, 200);

        return $this->handleView($view);
    }

    /**
     * Get single Language.
     *
     * @param int $id Id of the desired Language.
     *
     * @ApiDoc(
     *  description="Return single Language.",
     *  parameters={
     *      {"name"="LanguageId", "dataType"="integer", "required"=true, "description"="Language id."}
     *  }
     * )
     * @Get(options={"i18n"=false})
     */
    public function getLanguageAction($id)
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_VIEW);

        $language = $this->getLanguageById($id);
        $view = $this->view($language, 200);

        return $this->handleView($view);
    }

    /**
     * Save new Language.
     *
     * @ApiDoc(
     *  description="Create a new Language.",
     *  input="Webcook\Cms\I18nBundle\Form\Type\LanguageType",
     *  output="Webcook\Cms\I18nBundle\Entity\Language",
     * )
     * @Post(options={"i18n"=false})
     */
    public function postLanguagesAction()
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_INSERT);

        $response = $this->processLanguageForm(new Language(), 'POST');

        if ($response instanceof Language) {
            $statusCode = 200;
            $message = 'Language has been added.';
        } else {
            $statusCode = 400;
            $message = 'Error while adding new Language.';
        }

        $view = $this->getViewWithMessage($response, $statusCode, $message);

        return $this->handleView($view);
    }

    /**
     * Update Language.
     *
     * @param int $id Id of the desired Language.
     *
     * @ApiDoc(
     *  description="Update existing Language.",
     *  input="Webcook\Cms\I18nBundle\Form\Type\LanguageType",
     *  output="Webcook\Cms\I18nBundle\Entity\Language"
     * )
     * @Put(options={"i18n"=false})
     */
    public function putLanguageAction($id)
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_EDIT);

        try {
            $language = $this->getLanguageById($id, $this->getLockVersion((string) new Language()));
        } catch (NotFoundHttpException $e) {
            $language = new Language();
        }

        $response = $this->processLanguageForm($language, 'PUT');

        if ($response instanceof Language) {
            $statusCode = 204;
            $message = 'Language has been updated.';
        } else {
            $statusCode = 400;
            $message = 'Error while updating Language.';
        }

        $view = $this->getViewWithMessage($response, $statusCode, $message);

        return $this->handleView($view);
    }

    /**
     * Delete Language.
     *
     * @param int $id Id of the desired Language.
     *
     * @ApiDoc(
     *  description="Delete Language.",
     *  parameters={
     *     {"name"="LanguageId", "dataType"="integer", "required"=true, "description"="Language id."}
     *  }
     * )
     * @Delete(options={"i18n"=false})
     */
    public function deleteLanguageAction($id)
    {
        $this->checkPermission(WebcookCmsVoter::ACTION_DELETE);

        $language = $this->getLanguageById($id);

        $this->getEntityManager()->remove($language);
        $this->getEntityManager()->flush();

        $view = $this->getViewWithMessage(array(), 200, 'Language has been deleted.');

        return $this->handleView($view);
    }

    /**
     * Return form if is not valid, otherwise process form and return Language object.
     *
     * @param Language   $language
     * @param string     $method Method of request
     *
     * @return \Symfony\Component\Form\Form [description]
     */
    private function processLanguageForm(Language $language, String $method = 'POST')
    {
        $form = $this->createForm(LanguageType::class, $language);
        $form = $this->formSubmit($form, $method);
        if ($form->isValid()) {
            $language = $form->getData();

            if ($language instanceof Language) {
                $this->getEntityManager()->persist($language);
            }

            if ($language->isDefault()) {
                $languages = $this->getEntityManager()->getRepository('Webcook\Cms\I18nBundle\Entity\Language')->findBy(array(
                    'default' => true
                ));

                foreach ($languages as $l) {
                    if ($l !== $language) {
                        $l->setDefault(false);
                    }
                }
            }

            $this->getEntityManager()->flush();

            return $language;
        }

        return $form;
    }

    /**
     * Get Language by id.
     *
     * @param int $id [description]
     *
     * @return Language
     *
     * @throws NotFoundHttpException If Language doesn't exist
     */
    private function getLanguageById($id, $expectedVersion = null)
    {
        if ($expectedVersion) {
            $language = $this->getEntityManager()->getRepository('Webcook\Cms\I18nBundle\Entity\Language')->find($id, LockMode::OPTIMISTIC, $expectedVersion);
        } else {
            $language = $this->getEntityManager()->getRepository('Webcook\Cms\I18nBundle\Entity\Language')->find($id);
        }

        if (!$language instanceof Language) {
            throw new NotFoundHttpException('Language not found.');
        }

        $this->saveLockVersion($language);

        return $language;
    }
}
