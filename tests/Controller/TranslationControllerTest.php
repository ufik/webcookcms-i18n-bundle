<?php

namespace Webcook\Cms\I18nBundle\Tests\Controller;

class TranslationControllerTest extends \Webcook\Cms\CoreBundle\Tests\BasicTestCase
{
    public function testGetTranslations()
    {
        $this->createTestClient();
        $this->client->request('GET', '/api/translations');

        $translations = $this->client->getResponse()->getContent();

        $data = json_decode($translations, true);
        $this->assertCount(1, $data);
    }

    public function testGetTranslation()
    {
        $this->createTestClient();
        $this->client->request('GET', '/api/translations/1');
        $translation = $this->client->getResponse()->getContent();

        $data = json_decode($translation, true);

        $this->assertEquals(1, $data['id']);
        $this->assertEquals(1, $data['version']);
        $this->assertEquals('common.test.translation', $data['key']);
        $this->assertEquals('This is test translation.', $data['translation']);
        $this->assertEquals('messages', $data['catalogue']);
        $this->assertEquals('cs', $data['language']['locale']);
    }

    public function testPost()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'POST',
            '/api/translations',
            array(
                'translation' => array(
                    'key' => 'common.test.new',
                    'language' => 1,
                    'translation' => 'Another translation'
                ),
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $translations = $this->em->getRepository('Webcook\Cms\I18nBundle\Entity\Translation')->findAll();

        $this->assertCount(4, $translations);
        $this->assertEquals('common.test.new', $translations[1]->getKey());
        $this->assertEquals('Another translation', $translations[1]->getTranslation());
        $this->assertEquals('cs', $translations[1]->getLanguage()->getLocale());
        $this->assertEquals('messages', $translations[1]->getCatalogue());
    }

    public function testPut()
    {
        $this->createTestClient();

        $this->client->request('GET', '/api/translations/1'); // save version into session
        $crawler = $this->client->request(
            'PUT',
            '/api/translations/1',
            array(
                'translation' => array(
                    'key' => 'common.test.changed', // Really need to change key? Or prohibit
                    'language' => 1,
                    'translation' => 'Still same message, but different.'
                ),
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $translation = $this->em->getRepository('Webcook\Cms\CoreBundle\Entity\Translation')->find(1);

        $this->assertEquals('common.test.changed', $translation->getKey());
        $this->assertEquals('Still same message, but different.', $translation->getTranslation());
        $this->assertEquals('messages', $translation->getCatalogue());
        $this->assertEquals('cs', $translation->getLanguage()->getLocale());
    }

    public function testDelete()
    {
        $this->createTestClient();

        $crawler = $this->client->request('DELETE', '/api/translations/1');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Translations = $this->em->getRepository('Webcook\Cms\CoreBundle\Entity\translation')->findAll();

        $this->assertCount(0, $Translations);
    }

    public function testWrongPost()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'POST',
            '/api/translations',
            array(
                'translation' => array(
                    'n' => 'Tester',
                ),
            )
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testPutNonExisting()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'PUT',
            '/api/translations/2',
            array(
                'translation' => array(
                    'key' => 'common.test.nonexisting',
                    'language' => 2,
                    'translation' => 'New translation.'
                ),
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $translations = $this->em->getRepository('Webcook\Cms\CoreBundle\Entity\Translation')->findAll();

        $this->assertCount(2, $translations);
        $this->assertEquals('common.test.nonexisting', $translations[1]->getTitle());
        $this->assertEquals('New translation.', $translations[1]->getTranslation());
        $this->assertEquals('messages', $translations[1]->getCatalogue());
        $this->assertEquals('cs', $translations[1]->getLanguage()->getLocale());
    }

    public function testWrongPut()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'PUT',
            '/api/translations/1',
            array(
                'translation' => array(
                    'name' => 'Tester missing Translation field',
                ),
            )
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
