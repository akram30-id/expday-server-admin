<?php
defined('BASEPATH') or exit('No direct script access allowed');

// use chriskacerguis\RestServer\RestController;

class Auth extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */

    // constructor
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AuthModel');
    }


    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if ($this->session->userdata("token") != NULL) {
                $data['response'] = [
                    'status' => 200,
                    'message' => 'You have signed in',
                    'token' => $this->session->userdata("token"),
                    'username' => $this->session->userdata("username"),
                    'name' => $this->session->userdata("name"),
                    'picture' => $this->session->userdata("picture"),
                    'company' => $this->session->userdata("company"),
                    'role' => $this->session->userdata("role"),
                    'country' => $this->session->userdata("country"),
                    'address' => $this->session->userdata("address"),
                    'phone' => $this->session->userdata("phone"),
                    'email' => $this->session->userdata("email"),
                    'about' => $this->session->userdata("about"),
                ];
            } else {
                $data['response'] = [
                    'status' => 404,
                    'message' => 'Login first',
                ];
            }
        }
        // else if ($_SERVER['REQUEST_METHOD'] == "PATCH") {

        //     $username = $this->input->input_stream("username");
        //     $name = $this->input->input_stream("name");
        //     $about = $this->input->input_stream("about");
        //     $company = $this->input->input_stream("company");
        //     $role = $this->input->input_stream("role");
        //     $country = $this->input->input_stream("country");
        //     $address = $this->input->input_stream("address");
        //     $phone = $this->input->input_stream("phone");
        //     $email = $this->input->input_stream("email");
        //     $oldPicture = $this->input->input_stream("oldPicture");

        //     // $upload_file_print = $_FILES['filePrint']['name'];
        //     // if ($upload_file_print != "" || $upload_file_print != NULL) {
        //     //     $config['upload_path'] = FCPATH . '/assets/img/uploadPrint/';
        //     //     $config['allowed_types'] = 'jpg|png|jpeg|svg';
        //     //     $config['max_size'] = 5120;
        //     //     $config['file_name'] = 'file_print_' . time();

        //     //     $this->load->library('upload', $config);
        //     //     $this->upload->do_upload('filePrint');
        //     //     $filePrint = $this->upload->data('file_name');
        //     //     if ($oldPicture != NULL || $oldPicture != "") {
        //     //         unlink(FCPATH . '/assets/img/uploadPrint/' . $oldPicture);
        //     //     }
        //     // } else {
        //     //     $filePrint = $oldPicture;
        //     // }

        //     $config['upload_path'] = './assets/images/profile';
        //     $config['allowed_types'] = 'jpeg|jpg|png|svg';
        //     $config['max_size']  = '1024';
        //     $config['max_width']  = '2048';
        //     $config['max_height']  = '2048';
        //     $config['file_name'] = $username . "_" . date('d-m-Y');

        //     $this->load->library('upload', $config);

        //     if (!$this->upload->do_upload('file')) {

        //         $data['response'] = [
        //             'status' => 500,
        //             'message' => 'Update Picture Failed',
        //             'error' => $this->upload->display_errors()
        //         ];
        //         $dataupload['file_name'] = $oldPicture;

        //     } else {
        //         $dataupload = $this->upload->data();
        //         $data['response'] = [
        //             'status' => 200,
        //             'message' => 'Update Profile Success',
        //         ];
        //     }

        //     $dataUpdate = [
        //         'name' => $name,
        //         'about' => $about,
        //         'company' => $company,
        //         'role' => $role,
        //         'country' => $country,
        //         'address' => $address,
        //         'phone' => $phone,
        //         'email' => $email,
        //         'picture' => $dataupload['file_name']
        //     ];

        //     $this->AuthModel->updateProfile($username, $dataUpdate);

        // }
        else {
            $data['response'] = [
                'status' => 400,
                'message' => 'Bad Request',
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(($data));
    }

    public function login()
    {
        $username = html_escape($this->input->post("username"));
        $password = md5($this->input->post("password"));

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cek = $this->AuthModel->getAccount($username, $password)->num_rows();

            if ($cek > 0) {
                $token = $username . "-" . date('d-m-Y-H:i:s');
                $this->AuthModel->setTokenLogin($username, $password, $token);

                $query = $this->AuthModel->getAccount($username, $password);
                $account = $query->result();

                $dataSession = [
                    "token" => $token,
                    "username" => $username,
                    "name" => $account[0]->name,
                    "picture" => $account[0]->picture,
                    "company" => $account[0]->company,
                    "role" => $account[0]->role,
                    "country" => $account[0]->country,
                    "address" => $account[0]->address,
                    "phone" => $account[0]->phone,
                    "email" => $account[0]->email,
                    "about" => $account[0]->about,
                ];
                $this->session->set_userdata($dataSession);

                $data['response'] = [
                    'status' => 200,
                    'message' => 'login success',
                    'account' => [
                        "username" => $account[0]->username,
                        "token" => $account[0]->token
                    ],
                ];
            } else {
                $data['response'] = [
                    'status' => 404,
                    'message' => 'account not found',
                    'account' => 'N/A'
                ];
            }
        } else {
            $data['response'] = [
                'status' => 400,
                'message' => 'bad request',
            ];
        }

        // var_dump($cek);
        // die();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $username = $this->session->userdata("username");
            $this->AuthModel->setTokenLogout($username);
            $this->session->sess_destroy();

            $data = [
                "status" => 200,
                "message" => "logout success"
            ];
        } else {
            $data = [
                "status" => 400,
                "message" => "bad request"
            ];
        }
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] == "PUT") {

            $username = $this->input->input_stream("username");
            $name = $this->input->input_stream("name");
            $about = $this->input->input_stream("about");
            $company = $this->input->input_stream("company");
            $role = $this->input->input_stream("role");
            $country = $this->input->input_stream("country");
            $address = $this->input->input_stream("address");
            $phone = $this->input->input_stream("phone");
            $email = $this->input->input_stream("email");

            $dataUpdate = [
                'name' => $name,
                'about' => $about,
                'company' => $company,
                'role' => $role,
                'country' => $country,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
            ];

            $this->AuthModel->updateProfile($username, $dataUpdate);

            $this->session->unset_userdata(array_keys($dataUpdate));
            $this->session->set_userdata($dataUpdate);

            $data['response'] = [
                'status' => 200,
                'message' => "Success",
                'data' => $dataUpdate
            ];

        } else {
            $data['response'] = [
                'status' => 400,
                'message' => 'Bad Request',
            ];
        }

        header("Content-Type: multipart/form-data");
        echo json_encode($data);
    }

    public function updateProfilePicture()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $username = $this->input->post("username");
            $oldPicture = $this->input->post("oldPicture");

            $config['upload_path'] = './assets/img/profile';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']  = '1536';
            $config['max_width']  = '2048';
            $config['max_height']  = '2048';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('file')) {
                $data['response'] = [
                    'status' => 500,
                    'message' => 'Failed',
                    'error' => $this->upload->display_errors()
                ];
                $dataupload['file_name'] = $oldPicture;
            } else {
                $dataupload = $this->upload->data();

                $this->session->unset_userdata("picture");

                $this->session->set_userdata("picture", $dataupload['file_name']);

                if ($oldPicture != "-") {
                    unlink('./assets/img/profile/' . $oldPicture);
                } else {
                    echo "";
                }

                $data['response'] = [
                    'status' => 200,
                    'message' => 'Success',
                    'username' => $username,
                    'oldPicture' => $oldPicture,
                    'file_name' => $dataupload['file_name']
                ];
            }

            $dataUpdate = [
                'picture' => $dataupload['file_name'],
            ];

            $this->AuthModel->updateProfile($username, $dataUpdate);
        } else if ($_SERVER['REQUEST_METHOD'] == "PUT") {

            $oldPicture = $this->input->input_stream("oldPicture");
            $username = $this->input->input_stream("username");

            $this->AuthModel->updateProfile($username, ['picture' => '-']);

            $data['response'] = [
                'status' => 200,
                'message' => 'Remove Picture Success',
                'username' => $username,
                'old_picture' => $oldPicture
            ];

            if ($oldPicture != NULL || $oldPicture != "" || $oldPicture !== "-") {
                unlink('./assets/img/profile/' . $oldPicture);
            } else {
                echo "";
            }

            $this->session->unset_userdata("picture");
            $this->session->set_userdata("picture", "-");
        } else {
            $data['response'] = [
                'status' => 400,
                'message' => 'Bad Request',
            ];
        }

        header("Content-Type: application/json");
        echo json_encode($data);
    }

    public function changePassword ()
    {
        $username = $this->input->input_stream("username");
        $currentPassword = $this->input->input_stream("currentPassword");
        $newPassword = $this->input->input_stream("newPassword");

        $getCurrentAccount = $this->AuthModel->getCurrentPassword($username, $currentPassword);

        $cek = $getCurrentAccount->num_rows();

        if ($cek > 0) {
            
            $this->AuthModel->updatePassword($username, $newPassword);
            $data['response'] = [
                'status' => 200,
                'message' => 'Change Password Successfuly'
            ];
        } else {
            $data['response'] = [
                'status' => 404,
                'message' => 'Wrong current password'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        

    }
}
