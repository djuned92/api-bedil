<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	
	public function __construct()
		{
			parent::__construct();
			$this->load->library('push');
		}	
	/**
	* AUTH
	*/
	public function login()
	{
		$username 	= $this->input->post('username');
		$password 	= $this->input->post('password');
		$_token 	= $this->input->post('_token');
		$user 		= $this->global->check_login($username);
		
		if(!empty($user)) {
			$user = $this->global->getCond('user','*',['username'=>$username])->row_array();
			if($user['status'] == 0)
			{
				$response['code'] 	= 50;
				$response['error'] 	= TRUE;
				$response['message']= 'Belum melakukan verifikasi user';
			} else {
				if(password_verify($password, $user['password'])) {
					$muwakif = $this->global->getCond('muwakif','*',['user_id'=>$user['id']])->row_array();
					$pegawai = $this->global->getCond('pegawai','*',['user_id'=>$user['id']])->row_array();

					// set session
					$sess_data = [
						'id'		=> $user['id'],
						// 'muwakif_id'=> $muwakif['id'],
						'username'	=> $user['username'],
						'level'		=> $user['level_id'],
						'_token' 	=> $_token,
						'logged_in' => TRUE,
					];

					if($user['level_id'] == 3) {
						$sess_data['muwakif_id'] = $muwakif['id'];
					} else {
						$sess_data['pegawai_id'] = $pegawai['id'];
					}

					$this->session->set_userdata($sess_data);

					$data['_token'] = ($_token != null) ? $_token : NULL;
					(isset($user['id'])) ? $this->global->update('user', $data, array('id'=> $user['id'])) : '';

					$response['code']  	= 200;
					$response['error'] 	= FALSE;
					$response['user']  	= $sess_data;
				} else {
					$response['code'] 	= 400;
					$response['error'] 	= TRUE;
					$response['message']= 'Wrong password';
				}
			}
		} else {
			$response['code'] 	= 404;
			$response['error'] 	= TRUE;
			$response['message']= 'User not found';
		}

		echo json_encode($response);	
	}

	public function logout()
	{
		$id 		= $this->input->post('id');
		$username 	= $this->input->post('username');

		$user = $this->global->check_login($username);
		
		if($id != NULL) {
			// update last login
			// $data['last_login'] 	= date('Y-m-d H:i:s');
			$data['_token'] 		= NULL;
			(isset($user['id'])) ? $this->global->update('user', $data, array('id'=> $user['id'])) : '';

			$response['code']  	= 200;
			$response['error'] 	= FALSE;
			$response['message']= 'Success Logout';
		}

		echo json_encode($response);
	}

	public function aktivasi()
	{
		$kode_aktivasi = $this->input->post('kode_aktivasi');
		$aktivasi = $this->global->getCond('user','*',['kode_aktivasi' => $kode_aktivasi]);
		if($aktivasi->num_rows() > 0) {
			$data = [
				'status'	=> '1',
			];

			$this->global->update('user',$data, ['kode_aktivasi' => $kode_aktivasi]);
			// print_r($this->db->last_query());die();
			$response['code'] = 200;
			$response['error'] = FALSE;
			$response['message'] = 'Aktivasi user berhasil';
		} else {
			$response['code'] = 404;
			$response['error'] = TRUE;
			$response['message'] = 'Kode aktivasi salah';
		}
		echo json_encode($response);
	}

	public function register()
	{
		// print_r(phpinfo());die();
        $this->load->library('email');
		$config['protocol'] 	= 'smtp';
        $config['smtp_host'] 	= 'ssl://smtp.gmail.com';
        $config['smtp_port'] 	= '465';
        $config['smtp_user'] 	= 'ahmaddjunaedi92@gmail.com'; //bangzafran445@gmail.com
        $config['smtp_pass'] 	= 'djuned1!.,.,'; //bastol1234567
        // $config['mailpath']     = "/usr/sbin/sendmail"; // or "/usr/sbin/sendmail"
        $config['mailtype'] 	= 'html';
        $config['charset'] 		= 'iso-8859-1';
        $config['wordwrap'] 	= TRUE;
        $config['newline'] 		= "\r\n"; //use double quotes
        $this->email->initialize($config);
		$this->db->trans_begin();
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$options = [
		    'cost' => 11,
		    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
		];
		$password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
		
		$data_user = [
			'username'	=> $username,
			'password'	=> $password_hash,
			'level_id'	=> 3,
			'kode_aktivasi'	=> 'XYZ123',
		];

		$user_id = $this->global->create('user', $data_user, TRUE);

		$data_muwakif = [
			'user_id'		=> $user_id,
			'nama'			=> $this->input->post('nama'),
			'email'			=> $this->input->post('email'),
			'alamat'		=> $this->input->post('alamat'),
			'tanggal_lahir'	=> $this->input->post('tanggal_lahir'),
			'no_hp'			=> $this->input->post('no_hp'),
		];

		$this->global->create('muwakif', $data_muwakif);

		/*
		$user = $this->global->getCondJoin('user','user.kode_aktivasi, muwakif.email',
											['username' => $username],
											['muwakif'=>'user.id = muwakif.user_id'])->row_array();
		$to_email   = $user['email'];
		$message	= 'KODE AKTIVASI ANDA '.$user['kode_aktivasi'].'';
		// send email
		$this->email->from('ahmaddjunaedi92@gmail.com','Ahmad Djunaedi');
		$this->email->to($to_email);
		$this->email->subject('Aktivasi User');
		$this->email->message($message);

		if ( ! $this->email->send())
		{
			$response['code']	 = 400;
			$response['error']   = TRUE;
			$response['message'] = $this->email->print_debugger();
		} else {*/
			if ($this->db->trans_status() === FALSE) {
	            $this->db->trans_rollback();
	            
	            $response['code']		= 501;
				$response['error']		= FALSE;
				$response['message']	= 'Failed registered!';
	        } else {
	        	$this->db->trans_commit();
				
				$response['code']		= 200;
				$response['error']		= FALSE;
				$response['message']	= 'Success registered!';
	        }
		// }

		echo json_encode($response);
	}

	public function random_aktivasi($length)
	{
	    $data = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $string = '';
	    for($i = 0; $i < $length; $i++) {
	        $pos = rand(0, strlen($data)-1);
	        $string .= $data{$pos};
	    }
	    return $string;
	}
	/* end AUTH */

	/*
	* TRANSAKSI
	*/
	public function get_all_transaksi()
	{
		$transaksi = $this->global->get('transaksi');
		if($transaksi->num_rows() > 0) {
			$response['code']		= 200;
			$response['error']		= FALSE;
			$response['transaksi']	= $transaksi->result_array();
		} else {
			$response['code']		= 404;
			$response['error']		= TRUE;
			$response['message']	= 'Trasaction not found!';			
		}

		echo json_encode($response);
	}

	public function get_transaksi($muwakif_id)
	{
		$transaksi = $this->global->getCond('transaksi','*',['muwakif_id'=>$muwakif_id]);
		if($transaksi->num_rows() > 0) {
			$response['code']		= 200;
			$response['error']		= FALSE;
			$response['transaksi']	= $transaksi->result_array();
		} else {
			$response['code']		= 404;
			$response['error']		= TRUE;
			$response['message']	= 'Trasaction not found!';			
		}

		echo json_encode($response);

	}

	public function get_transaksi_by_id($id)
	{
		$transaksi = $this->global->getCond('transaksi', '*',['id'=>$id]);
		if($transaksi->num_rows() > 0) {
			$response['code']		= 200;
			$response['error']		= FALSE;
			$response['transaksi']	= $transaksi->row_array();
		} else {
			$response['code']		= 404;
			$response['error']		= TRUE;
			$response['message']	= 'Trasaction not found!';			
		}

		echo json_encode($response);
	}

	public function add_transaksi()
	{
		$muwakif_id 		= $this->input->post('muwakif_id');
		$jumlah_transaksi 	= $this->input->post('jumlah_transaksi');
		$tanggal_transaksi 	= $this->input->post('tanggal_transaksi');
		$jenis_transaksi 	= $this->input->post('jenis_transaksi');

		$data_transaksi = [
			'muwakif_id'		=> $muwakif_id,
			'jumlah_transaksi' 	=> $jumlah_transaksi,
			'tanggal_transaksi'	=> $tanggal_transaksi,
			'jenis_transaksi' 	=> $jenis_transaksi,
			'created_at'		=> date('Y-m-d H:i:s'),
		];
		$last_id = $this->global->create('transaksi', $data_transaksi, TRUE);

		$response['code']		= 200;
		$response['id']			= $last_id;
		$response['error']		= FALSE;
		$response['message']	= 'Success transaction!';

		echo json_encode($response);
	}

	// upload bukti pembayaran send notif ke bag. keuangan
	public function upload_bukti_transaksi()
	{
		$this->load->library('upload');

		$config['upload_path'] 		= './assets/images/transaksi/';
		$config['allowed_types'] 	= 'gif|jpg|png';
		$config['max_size']  		= 2048;
		// $config['max_width']  		= 1024;
		// $config['max_height']  		= 768;
		$config['encrypt_name'] 	= TRUE;

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload()){
			$error = array('error' => $this->upload->display_errors());
			$response['error'] = $error;
		} else {
			// print_r($this->upload->data());die();
			$id = $this->input->post('id');

			$data_transaksi = [
				'bukti_transaksi'	=> $this->upload->data('file_name'),
				'status'			=> 1,
			];

			$update = $this->global->update('transaksi', $data_transaksi, ['id'=>$id]);
		
			// push notif here
			$user = $this->global->getCond('user','*',['username'=>'keuangan'])->row_array();
			$this->push->setTitle('Transaksi Wakaf Baru')
					->setbody('Konfirmasi Wakaf')
					->fire($user['_token']);

			if($update == FALSE) {
				$response['code'] 	= 204;
				$response['error']	= TRUE;
				$response['message']= 'No content transaction to updated!';
			} else {
				$response['code'] 	= 200;
				$response['error']	= FALSE;
				$response['message']= 'Transaction has been updated!';
			}
		}
		echo json_encode($response);
	}

	/**
	*	update status transaksi validasi transaksi
	*	0. pending, 1. proses, 2. gagal, 3. berhasil
	*/
	public function validasi_transaksi()
	{
		$id 	= $this->input->post('id');
		$flag 	= $this->input->post('flag');
		if($flag == '3') {
			$data_transaksi = [
				'status'	=> 3,
			];
			
			$data_transaksi_berhasil = [
				'transaksi_id'	=> $id,
				'created_at'	=> date('Y-m-d H:i:s'),				
			];

			$this->global->create('transaksi_berhasil',$data_transaksi_berhasil);
		} else { // gagal
			$data_transaksi = [
				'status'	=> 2,
			];

			$data_transaksi_gagal = [
				'transaksi_id'	=> $id,
				'created_at'	=> date('Y-m-d H:i:s'),
			];

			$this->global->create('transaksi_gagal',$data_transaksi_gagal);
		}
		
		$update = $this->global->update('transaksi', $data_transaksi, ['id'=>$id]);
		// push notif
		$user = $this->global->getCondJoin('transaksi',
								'user.*, transaksi.id as transaksi_id',
								['transaksi.id'=>$id],
								['muwakif' => 'transaksi.muwakif_id = muwakif.id',
								'user' => 'muwakif.user_id = user.id'])->row_array();
		$this->push->setTitle('Transaksi Wakaf')
						->setbody('Transaksi Wakaf Berhasil')
						->setNotificationId ( $user['transaksi_id'] )
						->fire($user['_token']);

		// total wakaf
		$query = "SELECT sum(jumlah_transaksi) as total_wakaf
					FROM transaksi
					WHERE status = '3'";
		$total_wakaf = $this->db->query($query)->row_array();

		$data_total_wakaf = [
			'total_wakaf'	=> $total_wakaf['total_wakaf'],
			'created_at'	=> date('Y-m-d H:i:s'),
		];

		$this->global->update('total_wakaf', $data_total_wakaf, ['id'=>1]);

		if($update == FALSE) {
			$response['code'] 	= 204;
			$response['error']	= TRUE;
			$response['message']= 'No content transaction to updated!';
		} else {
			$response['code'] 	= 200;
			$response['error']	= FALSE;
			$response['message']= 'Status Transaction has been updated!';
		}

		echo json_encode($response);
	}

	// lpw berita
	public function get_all_berita()
	{
		$berita = $this->global->get('berita');
		if($berita->num_rows() > 0) {
			$response['code']		= 200;
			$response['error']		= FALSE;
			$response['berita']		= $berita->result_array();
		} else {
			$response['code']		= 404;
			$response['error']		= TRUE;
			$response['message']	= 'Berita not found!';			
		}

		echo json_encode($response);
	}

	public function add_lpw_berita()
	{
		$user_id = $this->session->id;
		$pegawai = $this->global->getCond('pegawai','*',['user_id'=>$user_id])->row_array();
		
		$this->db->trans_begin();
				
		$data_lpw = [
			'pegawai_id'	=> $pegawai['id'],
			'nomor_laporan'	=> $this->input->post('nomor_laporan'),
			'jenis_laporan'	=> $this->input->post('jenis_laporan'),
		];

		$lpw_id = $this->global->create('laporan_pemberdayaan_wakaf',$data_lpw, TRUE);

		$data_berita = [
			'lpw_id'		=> $lpw_id,
			'judul_berita'	=> $this->input->post('judul_berita'),
			'isi_berita'	=> $this->input->post('isi_berita'),
			'tanggal'		=> $this->input->post('tanggal'),
		];

		$this->global->create('berita', $data_berita);

		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            
            $response['code']		= 501;
			$response['error']		= FALSE;
			$response['message']	= 'Gagal menambahkan laporan pemberdayaan wakaf dan berita!';
        } else {
        	$this->db->trans_commit();
			
			$response['code']		= 200;
			$response['error']		= FALSE;
			$response['message']	= 'Berhasil menambahkan laporan pemberdayaan wakaf dan berita!';
			
        }

		echo json_encode($response);
	}

	public function get_berita_by_id($id)
	{
		$berita = $this->global->getCond('berita', '*',['id'=>$id]);
		if($berita->num_rows() > 0) {
			$response['code']		= 200;
			$response['error']		= FALSE;
			$response['berita']		= $berita->row_array();
		} else {
			$response['code']		= 404;
			$response['error']		= TRUE;
			$response['message']	= 'Berita not found!';			
		}

		echo json_encode($response);				
	}
	/* end TRANSAKSI */

	public function generate_password()
	{
		$password = 'admin123';
		$options = [
		    'cost' => 11,
		    'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
		];
		$password_hash = password_hash($password, PASSWORD_BCRYPT, $options);
		echo $password_hash;
	}

	public function test_push_notif()
	{
		/*
		$device_token = array('eY8kJhJ0xGo:APA91bE9LStefdlwCz1z4k1paz_90RRrTGCT_C6Rp1ku37z9pjXqPcJlL9LwxCH3YV0TVUoo8OPCzhUFcbun2ChWxxq837F5XvoevGhy4LQovmnRbTs0792XF-FUHKCVvChzmichGhcP','ep8sUcJUbV8:APA91bGA132rp61DX-qj0NJgu8X2EOpjNQUSJAIBC2N-69gsouYVbcTehu25iWG-onq8pbijZ4inwZwsvV1nsBpZTY-xrDpXVpMAT_GRormD4Q1eXDa5Ib2sFF_y_zpSt6EViceFsOqF');

		for ($i=0; $i < count($device_token); $i++) { 
			$this->push->setTitle('Transaksi Wakaf')
							->setbody('Transaksi Wakaf Berhasil')
							// ->setNotificationId ( $user['transaksi_id'] )
							->fire($device_token[$i])[$i];
		}
		*/
		$device_token_user = 'eY8kJhJ0xGo:APA91bE9LStefdlwCz1z4k1paz_90RRrTGCT_C6Rp1ku37z9pjXqPcJlL9LwxCH3YV0TVUoo8OPCzhUFcbun2ChWxxq837F5XvoevGhy4LQovmnRbTs0792XF-FUHKCVvChzmichGhcP';
		$id = '1';
		$this->push->setTitle('Transaksi Wakaf')
						->setbody('Transaksi Wakaf Berhasil')
						->setNotificationId ( $id )
						->fire($device_token_user);
	}

}

/* End of file Api.php */
/* Location: ./application/controllers/Api.php */

