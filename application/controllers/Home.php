<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
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

	function __construct()
	{
		parent::__construct();
		$this->load->model("HomeModel");
	}


	public function index()
	{

		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$date = $this->input->post("date");

			$this->HomeModel->insertVisitors($date);

			if ($date) {
				$data = [
					'status' => 200,
					'message' => 'success'
				];
			} else {
				$data = [
					'status' => 400,
					'message' => 'Invalid Input'
				];
			}
		} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
			$dataVisit = $this->HomeModel->getVisitors();

			$dataRange = $this->HomeModel->getRangeVisitors();

			if ($dataVisit) {
				$data = [
					'status' => 200,
					'message' => 'success',
					'data' => $dataVisit,
					'rangeByTime' => $dataRange
				];
			} else {
				$data = [
					'status' => 500,
					'message' => 'Internal Server Error'
				];
			}
		}


		header('Content-Type: application/json');
		echo json_encode($data);
	}
}
