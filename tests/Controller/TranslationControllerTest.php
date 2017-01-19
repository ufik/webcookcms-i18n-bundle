<?php

namespace Webcook\Cms\I18nBundle\Tests\Controller;

class TranslationControllerTest extends \Webcook\Cms\CoreBundle\Tests\BasicTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Webcook\Cms\I18nBundle\DataFixtures\ORM\LoadTranslationData'
        ));
    }

    public function testGetTranslations()
    {
        $this->createTestClient();
        $this->client->request('GET', '/api/translations.json');

        $translations = $this->client->getResponse()->getContent();

        $data = json_decode($translations, true);
        $this->assertCount(2, $data);
    }

    public function testGetTranslation()
    {
        $this->createTestClient();
        $this->client->request('GET', '/api/translations/1.json');
        $translation = $this->client->getResponse()->getContent();

        $data = json_decode($translation, true);

        $this->assertEquals(1, $data['id']);
        $this->assertEquals(1, $data['version']);
        $this->assertEquals('common.test.translation', $data['key']);
        $this->assertEquals('This is test translation.', $data['translation']);
        $this->assertEquals('messages', $data['catalogue']);
        //$this->assertEquals('en', $data['language']['locale']);
    }

    public function testPost()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'POST',
            '/api/translations.json',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json'
            ),
            json_encode(array(
                'key' => 'common.test.new',
                'catalogue' => 'messages',
                'language' => '/api/languages/1',
                'translation' => 'Another translation'
            ))
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $translations = $this->em->getRepository('Webcook\Cms\I18nBundle\Entity\Translation')->findAll();

        $this->assertCount(3, $translations);
        $this->assertEquals('common.test.new', $translations[2]->getKey());
        $this->assertEquals('Another translation', $translations[2]->getTranslation());
        $this->assertEquals('cs', $translations[2]->getLanguage()->getLocale());
        $this->assertEquals('messages', $translations[2]->getCatalogue());
    }

    public function testPut()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'PUT',
            '/api/translations/1.json',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json'
            ),
            json_encode(array(
                'key' => 'common.test.changed', // Really need to change key? Or prohibit
                'language' => '/api/languages/1',
                'catalogue' => 'messages',
                'translation' => 'Still same message, but different.'
            ))
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $translation = $this->em->getRepository('Webcook\Cms\I18nBundle\Entity\Translation')->find(1);

        $this->assertEquals('common.test.changed', $translation->getKey());
        $this->assertEquals('Still same message, but different.', $translation->getTranslation());
        $this->assertEquals('messages', $translation->getCatalogue());
        $this->assertEquals('cs', $translation->getLanguage()->getLocale());
    }

    public function testDelete()
    {
        $this->createTestClient();

        $crawler = $this->client->request('DELETE', '/api/translations/1.json');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Translations = $this->em->getRepository('Webcook\Cms\I18nBundle\Entity\translation')->findAll();

        $this->assertCount(1, $Translations);
    }

    public function testWrongPost()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'POST',
            '/api/translations.json',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json'
            ),
            json_encode(array(
                    'n' => 'Tester',
            ))
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testPutNonExisting()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'PUT',
            '/api/translations/3.json',
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json'
            ),
            json_encode(array(
                'key' => 'common.test.nonexisting',
                'language' => '/api/languages/2',
                'catalogue' => 'messages',
                'translation' => 'New translation.'
            ))
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testWrongPut()
    {
        $this->createTestClient();

        $this->markTestSkipped('Wrong put retuns 200.');
        $crawler = $this->client->request(
            'PUT',
            '/api/translations/1.json',
            array(
                'translation' => array(
                    'name' => 'Tester missing Translation field',
                ),
            )
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
