<?php

class Classified_AdvertControllerTest extends BaseTestCase
{
    private function _generateAdvertRequest($cat_id = null)
    {
        $faker = Faker\Factory::create();

        if (! $cat_id)
        {
            $cats = $this->em()->createQuery("select c from \Classified\Entity\Category c where c.postable = 1")
                ->getResult();
            $cat = $faker->randomElement($cats);
        }
        else
            $cat = $this->em()->find('\Classified\Entity\Category', $cat_id);

        $answers = array();

        $request = array(
            "title" => $faker->sentence(3),
            "description" => $faker->text,
            "price" => (integer) rand(1000, 100000),
            "category" => $cat->getId(),
            "answers" => $answers
        );

        return $request;
    }

    public function test_Can_Create_Advert()
    {
        $request = $this->_generateAdvertRequest();

        $this->request->setMethod('POST')
            ->setPost(array("advert" => $request));

        $this->dispatch('/classified/advert/create');

        $this->assertRedirectRegex('|/adverts/(\d+)/show|');
    }

    public function test_Try_To_Create_With_Empty_Title()
    {
        $request = $this->_generateAdvertRequest();
        unset($request['title']);

        $this->request->setMethod('POST')
            ->setPost(array("advert" => $request));

        $this->dispatch('/classified/advert/create');

        $this->assertModule('Classified');
        $this->assertController('advert');
        $this->assertAction('new');
        $this->assertNotRedirectRegex('|/adverts/(\d+)/show|');
        $this->assertContains('Ad title must be specified', $this->getResponse()->getBody());
    }

    public function test_Direct_Can_Create_Advert()
    {
        $request = $this->_generateAdvertRequest();

        // validations
        $aRepo = $this->em()->getRepository('Classified\Entity\Advert');
        $request = $aRepo->filter($request);
        $valid = $aRepo->validate($request);
        $messages = ($valid === true)? array() : $valid;

        if (! empty($messages))
            throw new Zend_Exception("Validation errors");

        $advert = new \Classified\Entity\Advert();
        $advert->populate($request);

        $this->em()->persist($advert);
        $this->em()->flush();

        $this->assertNotEmpty($advert->getId());
    }

    public function test_Direct_Try_To_Create_Advert_With_Empty_Title()
    {
        $request = $this->_generateAdvertRequest();
        unset($request['title']);

        // validations
        $aRepo = $this->em()->getRepository('Classified\Entity\Advert');
        $request = $aRepo->filter($request);
        $valid = $aRepo->validate($request);
        $messages = ($valid === true)? array() : $valid;

        $this->assertNotEmpty($messages);
    }

    public function test_Try_To_Create_Advert_With_Invalid_Price()
    {
        $request = $this->_generateAdvertRequest();
        $request['price'] = "invalid_string_price";

        $this->request->setMethod('POST')
            ->setPost(array("advert" => $request));

        $this->dispatch('/classified/advert/create');

        $this->assertModule('Classified');
        $this->assertController('advert');
        $this->assertAction('new');
        $this->assertNotRedirectRegex('|/adverts/(\d+)/show|');
        $this->assertContains('Ad price has invalid format', $this->getResponse()->getBody());
    }

    public function test_Direct_Try_To_Create_Advert_With_Invalid_Price()
    {
        $request = $this->_generateAdvertRequest();
        $request['price'] = "invalid_string_price";

        // validations
        $aRepo = $this->em()->getRepository('Classified\Entity\Advert');
        $request = $aRepo->filter($request);
        $valid = $aRepo->validate($request);
        $messages = ($valid === true)? array() : $valid;

        $this->assertNotEmpty($messages);
    }

    public function test_Try_To_Create_Advert_Without_Category()
    {
        $request = $this->_generateAdvertRequest();
        unset($request['category']);

        $this->request->setMethod('POST')
            ->setPost(array("advert" => $request));

        $this->dispatch('/classified/advert/create');

        $this->assertModule('Classified');
        $this->assertController('advert');
        $this->assertAction('new');
        $this->assertNotRedirectRegex('|/adverts/(\d+)/show|');
        $this->assertContains('Ad category must be specified', $this->getResponse()->getBody());
    }

    public function test_Direct_Try_To_Create_Advert_Without_Category()
    {
        $request = $this->_generateAdvertRequest();
        unset($request['category']);

        // validations
        $aRepo = $this->em()->getRepository('Classified\Entity\Advert');
        $request = $aRepo->filter($request);
        $valid = $aRepo->validate($request);
        $messages = ($valid === true)? array() : $valid;

        $this->assertNotEmpty($messages);
    }
}
