<?php

class User_Model_UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    public function validate($entity, $password_require = true)
    {
        $messages = array();

        $id = (empty($entity['id']))? 0 : $entity['id']; // user id

        $email_validator = new Zend_Validate_EmailAddress();
        if (empty($entity['email'])) // email must be set
            $messages[] = "Введите свой email адрес";
        elseif (! $email_validator->isValid($entity['email'])) // & must be valid email
            $messages[] = "Указанный email адрес не является валидным";
        elseif ($this->isTaken($entity['email'], $id)) // & must be unique
            $messages[] = "Email адрес занят";

        if ($password_require)
        {
            if (empty($entity['password'])) // password must be set
                $messages[] = "Введите пароль";
            elseif (strlen($entity['password']) < 4) // & must be at least 4 chars long
                $messages[] = "Пароль слишком короткий";
        }

        return ($messages == array())? true : $messages;
    }

    public function isTaken($email, $user_id = 0)
    {
        $exists = $this->_em->createQuery("select count(u.id) from \Entity\User u where u.deleted=0 and u.email=?1 and u.id!=?2")
            ->setParameter(1, $email)
            ->setParameter(2, $user_id)
            ->getSingleScalarResult();

        return ($exists > 0);
    }

    public function create($request)
    {
        $user = new Entity\User();
        $user->setEmail($request['email']);
        $slug = Core_Model_Functions::generateSlug($request['username']);
        $user->setSlug($slug);
        $salt = md5(time());
        $user->setSalt($salt);
        $user->setPassword(md5($salt . $request['password']));
        $user->setBlocked(false);
        $user->setVerified(false);
        $user->setDeleted(false);
        $user->setDoNotSpam(false);
        $user->setRole("member");

        $user->setDescription(@$request['description']);
        $user->setPhone(@$request['phone']);
        $user->setUrl(@$request['url']);

        $this->_em->persist($user);

        return $user;
    }

    public function update(\Entity\User $user, $request)
    {
        $user->setEmail($request['email']);
        $user->setUsername($request['username']);
        $slug = Core_Model_Functions::generateSlug($request['username']);
        $user->setSlug($slug);

        $user->setDescription(@$request['description']);
        $user->setPhone(@$request['phone']);
        $user->setUrl(@$request['url']);

        $this->_em->persist($user);
    }



    public function checkPassword(\Entity\User $user, $password)
    {
        return (md5($user->getSalt() . $password) == $user->getPassword());
    }

    public function toArray(\Entity\User $entity)
    {
        return array(
            "id" => $entity->getId(),
            "email" => $entity->getEmail(),
        );
    }
}