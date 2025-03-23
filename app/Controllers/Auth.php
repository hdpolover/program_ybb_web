<?php

namespace App\Controllers;

class Auth extends BaseController
{
    
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Sign In',
        ];

        return $this->render('auth/sign-in', $data);
    }

    public function authorize()
    {
        // $email = trim($this->request->getPost('email'));
        // $password = trim($this->request->getPost('password'));

        // if ($email == 'admin@themesbrand.com' && $password == '123456') {
        //     $session = session();
        //     $session->set('isLoggedIn', 1);
        //     return redirect()->to('/');
        // } else {
        //     return redirect()->back()->with('error', 'These credentials do not match our records.');
        // }
            return redirect()->to('dashboard');
    }

    public function logout()
    {
        $session = session();
        $session->remove('isLoggedIn');
        return redirect()->to('/login');
    }

}
