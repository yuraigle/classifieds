<?php

class User_RegistrationController extends Bisna\Controller\Action
{
    private function _getCaptcha()
    {
        $captcha = new Zend_Captcha_Image();
        $captcha->setFont(APPLICATION_PATH . '/../assets/fonts/FreeMono.ttf');
        $captcha->setImgDir(APPLICATION_PATH . '/../public/captcha/');
        $captcha->setTimeout(300); // 5min
        $captcha->setWordlen(4);
        $captcha->setWidth(280);

        return $captcha;
    }

    public function newAction()
    {
        // redirect if already logged in
        if ($this->_helper->currentUser())
        {
            $session = $this->_helper->currentSession();
            $session->write("messages", "ALREADY_LOGGED_IN");
            $session->write("messages_class", null);

            $this->_helper->redirector->gotoRoute(array(), "home", true);
        }

        if($this->getRequest()->getMethod() === 'POST' && ! $this->_getParam("status"))
        {
            $this->forward("create");
        }

        $this->view->user = $user = $this->_getParam("user");
        $this->view->captcha_id = $this->_getCaptcha()->generate();
    }

    public function createAction()
    {
        $session = $this->_helper->currentSession();
        try
        {
            $request = $this->_getParam("user");

            $valid = $this->em()->getRepository('User\Entity\User')
                ->validate($request);

            $messages = ($valid === true)? array() : $valid;

            if (! $this->_getCaptcha()->isValid($request['captcha']))
                $messages[] = "WRONG_CAPTCHA";

            if (! empty($messages))
                throw new Zend_Exception("Validation errors");

            // create user
            $user = new User\Entity\User();
            $user->populate($request);
            $this->em()->persist($user);

            // write in session
            $session = $this->_helper->currentSession();
            $session->setUser($user);
            $this->em()->persist($session);
            $this->em()->flush();

            // TODO: send verification email

            // messages in session
            $session->write("messages", "SIGNED_UP_OK");
            $session->write("messages_class", "success");

            $this->_helper->redirector->gotoRoute(array("action" => "edit", "id" => $user->getId()), "profile", true);
        }
        catch (Zend_Exception $e)
        {
            $session->write("messages", $messages);
            $session->write("messages_class", "error");

            $this->forward('new', null, null, array("user" => $request, "status" => "ERROR"));
        }
    }

    public function recaptchaAction()
    {
        $this->_helper->json(array(
            "id" => $this->_getCaptcha()->generate(),
        ));
    }

    // send a verification letter for current user
    public function sendLetterAction()
    {
        $this->_helper->checkMember(); // redirect to login if !logged in
        $user = $this->_helper->currentUser();

        $code = md5(rand());
        $user->setVerifyCode($code);
        $user->setVerifyLetterDate(new \DateTime());

        $this->em()->persist($user);
        $this->em()->flush();

        // TODO: send email
    }

    public function verifyAction()
    {
        $this->_helper->checkMember(); // redirect to login if !logged in

        $session = $this->_helper->currentSession();

        $id = $this->_getParam("id");
        $code = $this->_getParam("code");
        $day_ago = new \DateTime();
        $day_ago->sub(new \DateInterval("P1D"));

        $res = $this->em()->createQuery("select u from \User\Entity\User u"
            . " where u.id=?1 and u.verify_code=?2 and u.verify_letter_date>?3"
            . " and u.verified = 0")
            ->setParameter(1, $id)
            ->setParameter(2, $code)
            ->setParameter(3, $day_ago)
            ->getResult();

        if (empty($res[0]))
        {
            $this->view->status = "ERROR";
        }
        else
        {
            $this->view->status = "OK";

            $user = $res[0];
            $user->setVerified(true);
            $user->setVerifyCode(null);
            $this->em()->persist($user);
            $this->em()->flush();

            // messages in session
            $session->write("messages", "VERIFICATION_OK");
            $session->write("messages_class", "success");

            $this->_helper->redirector->gotoRoute(array("action" => "edit", "id" => $user->getId()), "profile", true);
        }

    }
}
