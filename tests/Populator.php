<?php
/**
 * Created by JetBrains PhpStorm.
 * User: reneaigle
 * Date: 3/7/13
 * Time: 7:48 PM
 * To change this template use File | Settings | File Templates.
 */

class Populator extends BaseTestCase
{
    public static $admin_email = "admin@example.com";
    public static $admin_password = "qwerty";
    public static $member_password = "asdasd";

    public function populate()
    {
        $this->setUp();

        $this->_populateCategories();
        $this->_populateQuestions();
        $this->_populateUsers();
    }

    private function _populateCategories()
    {
        $maxDepth = 3;
        $maxCount = 100;

        $faker = Faker\Factory::create();
        $categories = array();
        for ($i = 0; $i<$maxCount; $i++)
        {
            // 1/20 cats are top-level
            if (empty($categories) || rand(0, 100) < 5)
                $parent = null;
            else
            {
                $parentId = $categories[array_rand($categories)];
                $parent = $this->em()->find('\Classified\Entity\Category', $parentId);
            }

            $category = new \Classified\Entity\Category();
            $category->setName($faker->sentence(3));
            $category->setParent($parent);
            $category->setPostable(! is_null($parent));
            $category->setDomain(CURRENT_DOMAIN);

            $this->em()->persist($category);
            $this->em()->flush();

            if ($category->getDepth() < $maxDepth-1)
                $categories[] = $category->getId();
        }

        // clear category tree in cache
        $cache = \Zend_Registry::get('cache');
        $cache->remove('CATEGORIES_LIST');
    }

    public function _populateQuestions()
    {
        $categories = $this->em()->createQuery('select c from \Classified\Entity\Category c where c.parent is not null')
            ->getResult();

        $faker = Faker\Factory::create();

        for ($i = 0; $i < 200; $i++)
        {
            $question = new \Classified\Entity\Question();
            $type = $faker->randomElement(array ('text', 'textarea', 'select', 'ranged', 'checkbox'));
            $question->setType($type);
            $question->setName($faker->sentence(2));
            $question->setDescription($faker->text);
            $question->setRequired($faker->boolean);

            if ($type == 'select')
            {
                $count = (integer) rand(2, 8);
                $question->setPredefined(join("\n", $faker->words($count)));
            }

            $this->em()->persist($question);

            $cat1 = $faker->randomElement($categories);
            $ref = new \Classified\Entity\CategoryQuestionReference();
            $ref->setQuestion($question);
            $ref->setCategory($cat1);
            $ref->setWeight((integer) rand(1, 10));
            $this->em()->persist($ref);

            $cat2 = $faker->randomElement($categories);
            if ($cat2->getId() != $cat1->getId())
            {
                $ref = new \Classified\Entity\CategoryQuestionReference();
                $ref->setQuestion($question);
                $ref->setCategory($cat1);
                $ref->setWeight((integer) rand(1, 10));
                $this->em()->persist($ref);
            }
        }

        $this->em()->flush();
    }

    private function _populateUsers()
    {
        $faker = Faker\Factory::create();

        // populate admin
        $user = new \User\Entity\User();
        $user->setUsername("Admin");
        $user->setRole("admin");
        $user->setEmail(Populator::$admin_email);
        $user->setSalt($faker->md5);
        $user->setPassword(md5($user->getSalt() . Populator::$admin_password));
        $user->setPhone($faker->phoneNumber);
        $user->setDescription($faker->text);
        $user->setUrl($faker->url);
        $user->setVerified($faker->boolean);
        $user->setAllowLetters($faker->boolean);
        $user->setCreated($faker->dateTimeBetween('-2 years', 'now'));
        $this->em()->persist($user);

        for ($i = 0; $i < 5; $i++)
        {
            $user = new \User\Entity\User();
            $user->setUsername($faker->name);
            $user->setEmail($faker->email);
            $user->setSalt($faker->md5);
            $user->setPassword(md5($user->getSalt() . Populator::$member_password));
            $user->setPhone($faker->phoneNumber);
            $user->setDescription($faker->text);
            $user->setUrl($faker->url);
            $user->setVerified($faker->boolean);
            $user->setAllowLetters($faker->boolean);
            $user->setCreated($faker->dateTimeBetween('-2 years', 'now'));

            $this->em()->persist($user);
        }

        $this->em()->flush();
    }
}