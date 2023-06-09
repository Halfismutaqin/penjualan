<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    
    public function __construct(){
        helper('number');
        helper('form');
    }
    public function index_login()
    {

        $data['title'] = 'Login';
        $data['cart'] =\Config\Services::cart();


        return view('auth/login', $data);
    }

    public function login()
    {

        // validate input text
        $validationRule = [
            'identity' => [
                'rules' => 'required'
            ],
            'password' => [
                'rules' => 'required'
            ]
        ];

        if (!$this->validate($validationRule)) {
            $error = $this->validator->getErrors();
            $error_val = array_values($error);
            die(json_encode([
                'status' => false,
                'response' => $error_val[0]
            ])); 
        }       

        // input data
        $identity = $this->request->getPost('identity');
        $password = $this->request->getPost('password');        

        // load model
        $LoginModel = new \App\Models\Login();

        // find Login
        $Login = $LoginModel->select('id,username,password')->where('username', $identity)->orWhere('email', $identity)->first();

        // Login not found.
        if (!$Login) {
            return $this->response->setJSON([
                'status' => false,
                'response' => 'Account not found'
            ]);         
        }

        // validate password
        if (!password_verify($password, $Login['password'])) {
            // invalid password
            return $this->response->setJSON([
                'status' => false,
                'response' => 'Password Invalid'
            ]);     
        }

        // build data
        $data = [
            'id' => $Login['id'],
            'username' => $Login['username'],            
        ];

        // set session
        session()->set('auth', $data);

        // check if remember exist
        if ($this->request->getPost('remember')) {

            // load helper
            helper('aeshash');

            // set cookie
            $auth_hash = aeshash('enc', json_encode($_SESSION['auth']) , config('Encryption')->key);
            setcookie('auth', $auth_hash, time() + (86400 * 30), '/');      
        }


        // send response
        return $this->response->setJSON([
            'status' => true,
            'response' => 'Success Login',
            'redirect' => base_url('product')
        ]);     
    }

    public function index_register()
    {

        $data['title'] = 'Register';

        return view('auth/register', $data);
    }   

    public function register()
    {
        // $data['cart'] =\Config\Services::cart();


        // validate input text
        $validationRule = [
            'email' => [
                'rules' => 'required|max_length[100]|valid_email|is_unique[Login.email]'
            ],
            'username' => [
                'rules' => 'required|min_length[4]|max_length[30]|is_unique[Login.username]'
            ],
            'password' => [
                'rules' => 'required|min_length[4]|max_length[50]'
            ],
            'password_confirm' => [
                'rules' => 'matches[password]'
            ]            
        ];

        if (!$this->validate($validationRule)) {
            $error = $this->validator->getErrors();
            $error_val = array_values($error);
            die(json_encode([
                'status' => false,
                'response' => $error_val[0]
            ])); 
        }           

        // input data
        $data['email'] = $this->request->getPost('email');        
        $data['username'] = $this->request->getPost('username');
        $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

        // load model
        $LoginModel = new \App\Models\Login();    

        // insert data
        $register = $LoginModel->insert($data);

        // build data
        $data = [
            'id' => $register,
            'username' => $data['username'],                
        ];

        // set session
        session()->set('auth', $data);

        // send response
        return $this->response->setJSON([
            'status' => true,
            'response' => 'Success Register',
            'redirect' => base_url('product')
        ]); 
    }     

    public function logout()
    {
        session()->remove('auth');
        setcookie('auth', null, -1, '/'); 
        return redirect()->to(base_url('login'));       
    }
}