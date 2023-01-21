<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AuthModel extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function getAccount($username, $password) 
    {
        return $this->db->query("SELECT * FROM auth WHERE username='$username' AND password='$password'");
    }

    function setTokenLogin($username, $password, $token) {
        return $this->db->update('auth', ['token' => $token], ['username' => $username, 'password' => $password]);
    }

    function setTokenLogout($username)
    {
        return $this->db->update('auth', ['token' => '-'], ['username' => $username]);
    }

    function updateProfile($username, $dataUpdate)
    {
        return $this->db->update('auth', $dataUpdate, ['username' => $username]);
    }

    function getCurrentPassword ($username, $currentPassword) 
    {
        return $this->db->get_where('auth', ['username' => $username, 'password' => md5($currentPassword)]);
    }

    function updatePassword ($username, $newPassword)
    {
        return $this->db->update('auth', ['password' => md5($newPassword)], ['username' => $username]);
    }

}
