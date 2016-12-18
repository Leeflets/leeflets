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

        // TODO: check if user is already logged in

        return $this->createHtmlResponse('signin', [
            'form' => $loginForm
        ]);
    }

    /**
     * @param Request $request
     */
    public function loginAction(Request $request) {
        $loginForm = $this->getLoginForm();

    }

    /**
     * @return Form
     */
    private function getLoginForm() {
        $form = new Form([
            'class' => 'login-container'
        ]);

        $form->add(new EmailField('email', [
            'placeholder' => 'Email'
        ]));
        $form->add(new PasswordField('password', [
            'placeholder' => 'Password'
        ]));

        $form->add(new SubmitButton('Log In'));

        return $form;
    }
}
