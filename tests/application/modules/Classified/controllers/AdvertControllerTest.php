<?php

class Classified_AdvertControllerTest extends BaseTestCase
{
    private function _generateAdvertRequest($cat_id = null)
    {
        $faker = Faker\Factory::create();
        $cRepo = $this->em()->getRepository('\Classified\Entity\Category');

        if (! $cat_id)
        {
            $cats = $this->em()->createQuery("select c from \Classified\Entity\Category c where c.postable = 1")
                ->getResult();
            $cat = $faker->randomElement($cats);
        }
        else
            $cat = $cRepo->find($cat_id);

        $answers = array();
        $questions = $cRepo->getTiedQuestions($cat->getId());
        if (! empty($questions))
        {
            foreach ($questions as $q)
            {
                switch ($q->getType()) {
                    case 'text':
                        $answers[$q->getId()] = $faker->sentence(3);
                        break;
                    case 'ranged':
                        $answers[$q->getId()] = (integer) rand(100, 1000);
                        break;
                    case 'textarea':
                        $answers[$q->getId()] = $faker->text;
                        break;
                    case 'checkbox':
                        $answers[$q->getId()] = $faker->boolean;
                        break;
                    case 'select':
                        $answers[$q->getId()] = $faker->randomElement(preg_split("/\n/", $q->getPredefined()));
                        break;
                }
            }
        }

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

    public function test_Can_Remove_Advert()
    {
        // login
        $this->loginUser(\Populator::$admin_email, \Populator::$admin_password);

        $this->resetRequest();
        $this->resetResponse();

        // create advert first
        $request = $this->_generateAdvertRequest();

        // validations
        $aRepo = $this->em()->getRepository('Classified\Entity\Advert');
        $request = $aRepo->filter($request);
        $valid = $aRepo->validate($request);
        $messages = ($valid === true)? array() : $valid;

        $this->assertEmpty($messages);

        $advert = new \Classified\Entity\Advert();
        $advert->populate($request);
        $this->em()->persist($advert);
        $this->em()->flush();

        $ad_id = $advert->getId();
        $this->assertNotEmpty($ad_id);

        // remove then
        $this->request->setMethod('POST')
            ->setPost(array('id' => $ad_id));

        $this->dispatch("/adverts//delete");

        $this->assertEmpty($aRepo->find($ad_id));
    }

    public function test_Remove_Advert_Causes_Removing_Answers()
    {
        // login
        $this->loginUser(\Populator::$admin_email, \Populator::$admin_password);
        $this->resetRequest();
        $this->resetResponse();

        // create advert first
        // select category with several tied questions
        $refs = $this->em()->createQuery("select r from \Classified\Entity\CategoryQuestionReference r")
            ->getResult();
        $faker = Faker\Factory::create();
        $cat_id = $faker->randomElement($refs)->getCategory()->getId();

        // answers & questions count
        $ansCount1 = $this->em()->createQuery("select count(a) from \Classified\Entity\Answer a")
            ->getSingleScalarResult();
        $qCount1 = $this->em()->createQuery("select count(q) from \Classified\Entity\Question q")
            ->getSingleScalarResult();

        $request = $this->_generateAdvertRequest($cat_id);

        // validations
        $aRepo = $this->em()->getRepository('Classified\Entity\Advert');
        $request = $aRepo->filter($request);
        $valid = $aRepo->validate($request);
        $messages = ($valid === true)? array() : $valid;

        $this->assertEmpty($messages);

        $advert = new \Classified\Entity\Advert();
        $advert->populate($request);
        $this->em()->persist($advert);
        $this->em()->flush();

        $ad_id = $advert->getId();
        $this->assertNotEmpty($ad_id);

        // answers & questions count
        $ansCount2 = $this->em()->createQuery("select count(a) from \Classified\Entity\Answer a")
            ->getSingleScalarResult();
        $qCount2 = $this->em()->createQuery("select count(q) from \Classified\Entity\Question q")
            ->getSingleScalarResult();

        $this->assertTrue($ansCount2 > $ansCount1);
        $this->assertTrue($qCount2 == $qCount1);

        $this->request->setMethod('POST')
            ->setPost(array('id' => $ad_id));

        $this->dispatch("/adverts//delete");

        $this->assertEmpty($aRepo->find($ad_id));

        // answers & questions count
        $ansCount3 = $this->em()->createQuery("select count(a) from \Classified\Entity\Answer a")
            ->getSingleScalarResult();
        $qCount3 = $this->em()->createQuery("select count(q) from \Classified\Entity\Question q")
            ->getSingleScalarResult();

        $this->assertTrue($ansCount3 == $ansCount1);
        $this->assertTrue($qCount3 == $qCount1);
    }
}
