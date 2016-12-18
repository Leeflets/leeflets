<?php

namespace Leeflets\Controller;

use Leeflets\Core\Response;
use Leeflets\Form\EmailField;
use Leeflets\Form\Form;
use Leeflets\Form\PasswordField;
use Leeflets\Form\SubmitButton;
use Widi\Components\Router\Request;

/**
 *
 *
 * @package Leeflets\Controller
 */
class SignInController extends AbstractController {

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request) {
        $loginForm = $this->getLoginForm();

        if($this->session->exists('user')) {
            $this->redirect('/');
        }

        return $this->createHtmlResponse('signin',
            array_merge(
                $this->getBasicContext(),
                [
                    'form' => $loginForm
                ]
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request) {
        $loginForm = $this->getLoginForm();

        // validate form
        $validatedData = $loginForm->validate($request);

        // if valid
        if ($loginForm->isValid() && $this->correctLogin($validatedData['email'], $validatedData['password'])) {
            // login and redirect to
            $this->session->set('user', true);

            $this->redirect('/');
        }

        // otherwise re-render form with errors
        return $this->createHtmlResponse('signin',
            array_merge(
                $this->getBasicContext(),
                [
                    'form' => $loginForm
                ]
            )
        );
    }

    /**
     * @return Form
     */
    private function getLoginForm() {
        $form = new Form([
            'class' => 'login-container'
        ]);

        $form->add(new EmailField('email', [
            'placeholder' => 'Email',
            'required' => true
        ]));

        $form->add(new PasswordField('password', [
            'placeholder' => 'Password',
            'required' => true
        ]));

        $form->add(new SubmitButton('Log In'));

        return $form;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return bool
     */
    private function correctLogin($email, $password) {
        return $email === '1blankz7@googlemail.com' && $password === 'hello';
    }
}
