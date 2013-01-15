<?php

class User_Model_User extends \Doctrine\ORM\EntityRepository
{
    public function expr() {}

    /*
     * checks given password
     */
    public function checkPassword(User\Entity\User $user, $password)
    {
        return (md5($user->getSalt() . $password) == $user->getPassword());
    }

    public function isTaken($email, $user_id = 0)
    {
        $exists = $this->_em->createQuery("select count(u.id) from \User\Entity\User u where u.deleted=0 and u.email=?1 and u.id!=?2")
            ->setParameter(1, $email)
            ->setParameter(2, $user_id)
            ->getSingleScalarResult();

        return ($exists > 0);
    }

    public function checkExistence($email)
    {

    }

    public function authenticate($email, $password)
    {
        $user = $this->findOneBy(array("email"=>$email, "deleted"=>0));

        $messages = array();
        if (is_null($user))
            $messages[] = "USER_NOT_FOUND";
        elseif (!$this->checkPassword($user, $password))
            $messages[] = "PASSWORD_WRONG";

        return ($messages == array())? $user : $messages;
    }

    public function validate($request, $password_req = true)
    {
        $messages = array();

        $id = (empty($request['id']))? 0 : $request['id']; // user id

        if (empty($request['username']))
            $messages[] = "USERNAME_BLANK";

        $email_validator = new Zend_Validate_EmailAddress();
        if (empty($request['email'])) // email must be set
            $messages[] = "EMAIL_BLANK";
        elseif (! $email_validator->isValid($request['email'])) // & must be valid email
            $messages[] = "EMAIL_NOT_VALID";
        elseif ($this->isTaken($request['email'], $id)) // & must be unique
            $messages[] = "EMAIL_TAKEN";

        if ($password_req)
        {
            if (empty($request['password'])) // password must be set
                $messages[] = "PASSWORD_BLANK";
            elseif (strlen($request['password']) < 4) // & must be at least 4 chars long
                $messages[] = "PASSWORD_TOO_SHORT";
        }

        return ($messages == array())? true : $messages;
    }

    public function create($request)
    {
        $user = new \User\Entity\User();
        $user->setEmail($request['email']);
        $user->setUsername($request['username']);

        // salt & password
        $salt = md5(rand());
        $user->setSalt($salt);
        $user->setPassword(md5($salt . $request['password']));

        $user->setDeleted(false);

        $this->_em->persist($user);
        return $user;
    }
}