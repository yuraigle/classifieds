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

    public function signupAction()
    {
        // redirect if already logged in
        if ($this->_helper->currentUser())
        {
            $this->_helper->messages("ALREADY_LOGGED_IN");
            $this->_helper->redirector->gotoRoute(array(), "home", true);
        }

        $captcha = $this->_getCaptcha();

        if($this->getRequest()->getMethod() === 'POST')
        {
            try
            {
                $request = $this->_getParam("user");

                $valid = $this->em()->getRepository('User\Entity\User')
                    ->validate($request);

                $messages = ($valid === true)? array() : $valid;

                if (! $captcha->isValid($request['captcha']))
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

                // messages in session
                $session->write("messages", "SIGNED_UP_OK");
                $session->write("messages_class", "success");

                $this->_helper->redirector->gotoRoute(array(), "home", true);
            }
            catch (Zend_Exception $e)
            {
                $this->view->messages = $messages;
                $this->view->messages_class = "error";
            }
        }

        $this->view->user = $user = $this->_getParam("user");
        $this->view->captcha_id = $captcha->generate();
    }

    public function recaptchaAction()
    {
        $this->_helper->json(array(
            "id" => $this->_getCaptcha()->generate(),
        ));
    }
}
