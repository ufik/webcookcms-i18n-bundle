<?php

namespace Webcook\Cms\I18nBundle\Tests\Controller;

class LanguageControllerTest extends \Webcook\Cms\CoreBundle\Tests\BasicTestCase
{
    public function testGetLanguages()
    {
        $this->createTestClient();
        $this->client->request('GET', '/api/languages');

        $languages = $this->client->getResponse()->getContent();

        $data = json_decode($languages, true);
        $this->assertCount(3, $data);
    }

    public function testGetlanguage()
    {
        $this->createTestClient();
        $this->client->request('GET', '/api/languages/1');
        $language = $this->client->getResponse()->getContent();

        $data = json_decode($language, true);

        $this->assertEquals(1, $data['id']);
        $this->assertEquals(1, $data['version']);
        $this->assertEquals('Čeština', $data['title']);
        $this->assertEquals('cs', $data['abbr']);
    }

    public function testPost()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'POST',
            '/api/languages',
            array(
                'language' => array(
                    'title' => 'Test lang',
                    'abbr' => 'tl',
                    'default' => true
                ),
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $languages = $this->em->getRepository('Webcook\Cms\I18n\Entity\Language')->findAll();

        $this->assertCount(4, $languages);
        $this->assertEquals('Test lang', $languages[3]->getTitle());
        $this->assertEquals('tl', $languages[3]->getAbbr());
        $this->assertTrue($languages[3]->isDefault());
        $this->assertFalse($languages[0]->isDefault());
    }

    public function testPut()
    {
        $this->createTestClient();

        $this->client->request('GET', '/api/languages/2'); // save version into session
        $crawler = $this->client->request(
            'PUT',
            '/api/languages/2',
            array(
                'language' => array(
                    'title' => 'English updated',
                    'abbr' => 'en',
                    'default' => true
                ),
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $language = $this->em->getRepository('Webcook\Cms\I18n\Entity\Language')->find(2);
        $languages = $this->em->getRepository('Webcook\Cms\I18n\Entity\Language')->findAll();

        $this->assertEquals('English updated', $language->getTitle());
        $this->assertEquals('en', $language->getAbbr());
        $this->assertTrue($language->isDefault());
        $this->assertFalse($languages[0]->isDefault());
    }

    public function testDelete()
    {
        $this->createTestClient();

        $crawler = $this->client->request('DELETE', '/api/languages/2');

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $Languages = $this->em->getRepository('Webcook\Cms\I18n\Entity\Language')->findAll();

        $this->assertCount(2, $Languages);
    }

    public function testWrongPost()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'POST',
            '/api/languages',
            array(
                'language' => array(
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
            '/api/languages/4',
            array(
                'language' => array(
                    'title' => 'Spanish',
                    'abbr' => 'es',
                    'default' => true
                ),
            )
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $languages = $this->em->getRepository('Webcook\Cms\I18n\Entity\Language')->findAll();

        $this->assertCount(4, $languages);
        $this->assertEquals('Spanish', $languages[3]->getTitle());
        $this->assertEquals('es', $languages[3]->getAbbr());
        $this->assertFalse($languages[0]->isDefault());
    }

    public function testWrongPut()
    {
        $this->createTestClient();

        $crawler = $this->client->request(
            'PUT',
            '/api/languages/1',
            array(
                'language' => array(
                    'name' => 'Tester missing Language field',
                ),
            )
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
