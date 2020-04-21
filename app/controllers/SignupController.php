<?php

use Phalcon\Mvc\Controller;
namespace App\Controllers;
use App\Models\Users as Users;
class SignupController extends ControllerBase
{
    public function indexAction()
    {

    }
    public function registerAction()
    {
        $user = new Users();

        //assign value from the form to $user
        $user->assign(
            $this->request->getPost(),
            [
                'nama',
                'email',
                'pass',
                'alamat',
                'no_hp'
            ]
        );
        // Store and check for errors
        $success = $user->save();

        // passing the result to the view
        $this->view->success = $success;

        if ($success) {
            $message = "Thanks for registering!";
        } else {
            $message = "Sorry, the following problems were generated:<br>"
                     . implode('<br>', $user->getMessages());
        }

        // passing a message to the view
        $this->view->message = $message;
    }
}