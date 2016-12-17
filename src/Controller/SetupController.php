<?php

namespace Leeflets\Controller;

use Leeflets\Core\Library\Controller;
use Leeflets\Core\Library\Form;
use Leeflets\Core\Library\Router;
use Phpass\Hash;

class SetupController extends AbstractController {

    protected $no_auth_actions = ['install'];

    function matching_passwords($password2, $password1) {
        return ($_POST['password1'] == $_POST['password2']);
    }

    function install() {
        if ($this->config->is_loaded) {
            die("Oops, there's already a config.php file. You'll need to remove it to run this installer.");
        }

        $password_min_length = 5;
        $password_max_length = 72;

        $form = new Form($this->config, $this->router, $this->settings, 'install-form', [
            'elements' => [
                'credentials' => [
                    'type' => 'fieldset',
                    'elements' => [
                        'username' => [
                            'type' => 'email',
                            'placeholder' => 'Email Address',
                            'class' => 'input-block-level',
                            'required' => true
                        ],
                        'password1' => [
                            'type' => 'password',
                            'placeholder' => 'Password',
                            'class' => 'input-block-level',
                            'required' => true,
                            'validation' => [
                                [
                                    'callback' => 'min_length',
                                    'msg' => 'Sorry, your password must be at least ' . $password_min_length . ' characters in length.',
                                    'args' => [$password_min_length]
                                ],
                                [
                                    'callback' => 'max_length',
                                    'msg' => 'Sorry, your password can be no longer than ' . $password_max_length . ' characters in length.',
                                    'args' => [$password_max_length]
                                ]
                            ]
                        ],
                        'password2' => [
                            'type' => 'password',
                            'placeholder' => 'Confirm Password',
                            'class' => 'input-block-level',
                            'required' => true,
                            'validation' => [
                                [
                                    'callback' => [$this, 'matching_passwords'],
                                    'msg' => 'Your passwords do not match. Please enter matching passwords.',
                                    'args' => [$_POST['password2']]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $elements['buttons'] = [
            'type' => 'fieldset',
            'elements' => [
                'submit' => [
                    'type' => 'button',
                    'button-type' => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => 'Install Leeflets'
                ]
            ]
        ];

        $form->add_elements($elements);

        if ($form->validate()) {
            $hash = new Hash();

            $data = [
                'username' => $_POST['credentials']['username'],
                'password' => $hash->hashPassword($_POST['credentials']['password1'])
            ];

            $this->config->write($this->filesystem, $data);

            if (isset($_POST['connection']['type'])) {
                $this->settings->save_connection_info($_POST, $this->filesystem);
            }

            Router::redirect($this->router->adminUrl('/user/login/'));
            exit;
        }

        $args = compact('form');

        $args['page-title'] = 'Install';
        $args['layout'] = 'logged-out';

        return $args;
    }
}