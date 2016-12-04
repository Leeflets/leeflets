<?php

namespace Leeflets\Core\Controller;

use Leeflets\Core\Library\Controller;
use Leeflets\Core\Library\Form;
use Leeflets\Core\Library\Router;
use Phpass\Hash;

class UserController extends Controller {

    protected $no_auth_actions = ['login'];

    function login() {

        $form = new Form($this->config, $this->router, $this->settings, 'login-form', [
            'elements' => [
                'credentials' => [
                    'type' => 'fieldset',
                    'elements' => [
                        'username' => [
                            'type' => 'email',
                            'placeholder' => 'Email Address',
                            'class' => 'input-block-level',
                            'required' => true,
                            'autofocus' => true,
                            'validation' => [
                                [
                                    'callback' => [$this, '_check_username'],
                                    'msg' => 'Sorry, that is not the correct username.'
                                ]
                            ]
                        ],
                        'password' => [
                            'type' => 'password',
                            'placeholder' => 'Password',
                            'class' => 'input-block-level',
                            'required' => true,
                            'validation' => [
                                [
                                    'callback' => [$this, '_check_password'],
                                    'msg' => 'Sorry, that is not the correct password.'
                                ]
                            ]
                        ]
                    ]
                ],
                'buttons' => [
                    'type' => 'fieldset',
                    'elements' => [
                        'submit' => [
                            'type' => 'button',
                            'button-type' => 'submit',
                            'class' => 'btn btn-primary',
                            'value' => 'Login'
                        ]
                    ]
                ]
            ]
        ]);

        if ($form->validate()) {
            $this->user->set_cookie();
            Router::redirect($this->router->adminUrl());
            exit;
        }

        $args = compact('form');

        $args['page-title'] = 'Login';
        $args['layout'] = 'logged-out';

        return $args;
    }

    function logout() {
        $this->user->clear_cookie();

        if (isset($_GET['redirect']) && '' != $_GET['redirect']) {
            $url = $_GET['redirect'];
        } else {
            $url = $this->router->adminUrl('/user/login/');
        }

        $this->router->redirect($url);
        exit;
    }

    function _check_username($value) {
        return ($this->config->username == $value);
    }

    function _check_password($value) {
        $hash = new Hash();
        return $hash->checkPassword($value, $this->config->password);
    }
}
