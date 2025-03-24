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

    // sign up
    public function signUp()
    {
        $data = [
            'title' => 'Sign Up',
        ];

        return $this->render('auth/sign-up', $data);
    }

    public function signOut()
    {
        // $session = session();
        // $session->remove('isLoggedIn');
        // return redirect()->to('/login');
        $data = [
            'title' => 'Sign Out',
        ];

        return $this->render('auth/sign-out', $data);
    }

    // forgot password
    public function forgotPassword()
    {
        $data = [
            'title' => 'Forgot Password',
        ];

        return $this->render('auth/pass-reset', $data);
    }

    // reset password
    public function resetPassword()
    {
        $data = [
            'title' => 'Reset Password',
        ];

        return $this->render('auth/pass-change', $data);
    }

    // two step verification
    public function twoStepVerification()
    {
        $data = [
            'title' => 'Two Step Verification',
        ];

        return $this->render('auth/two-step', $data);
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



}
