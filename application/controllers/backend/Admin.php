<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller{

	private $session_id;

	public function __construct() {
		parent::__construct();
		$this->layout->setLayout('tmp_login');
		$this->layout->css(array(
			base_url().'assets/formvalidation.io-0.7/css/formValidation.min.css',
			base_url().'assets/bootstrap-3.3.6/css/bootstrap.min.css',
			base_url().'assets/font-awesome-4.5.0/css/font-awesome.min.css',
			base_url().'assets/wow/css/estilos.css'	            
		));
		$this->layout->js(array(
			base_url().'assets/jquery-2.1.4/jquery-2.1.4.min.js',
			base_url().'assets/formvalidation.io-0.7/js/formValidation.min.js',
			base_url().'assets/bootstrap-3.3.6/js/bootstrap.min.js',
			base_url().'assets/wow/js/admin/app.js'	            
		));
        $this->layout->setTitle('Ingresar | Wow Share');
		$this->load->model('backend/Admin_model');
		$this->session_id = $this->session->userdata('email');
	}

	public function index() {
		if (!empty($this->session_id)) {
			$this->layout->setLayout('tmp_admin');
        	$this->layout->setTitle('Panel Administración | Wow Share');
			$datos['paises'] = $this->Admin_model->listarDatos('pais');
			$datos['entrada'] = $this->Admin_model->listarDatos('tipo_noticia');
			$datos['membresia'] = $this->Admin_model->listarDatos('membresia');
			$datos['session'] = $this->session_id;
			$this->layout->view('admin', $datos);
            
		}
		else{
			redirect(base_url().'backend/admin/login', 301);
		}
	}

	function login(){
		if ($this->input->post()) {
			//die(sha1($this->input->post('pass', TRUE)));
			$datos = $this->Admin_model->login($this->input->post('email', TRUE), sha1($this->input->post('pass', TRUE)));
			//echo $datos; exit;
			if ($datos == 1) {
				$this->session->set_userdata("wow");
                $this->session->set_userdata('email', $this->input->post('email', TRUE));
                redirect(base_url().'backend/admin',  301);
			}
			else{
				$this->session->set_flashdata('ControllerMessage', 'Usuario y/o clave inválida.');
				//redirect(base_url().'backend/admin/login',  301);
			}
		}
		$this->layout->view('login');
	}

	function logout(){
		$this->session->unset_userdata(array('email' => ''));
		$this->session->sess_destroy("wow");
		redirect(base_url().'backend/admin/login',  301);
	}

	//Tabla de Datos
	function gestionarUsuarios(){
		if ($this->input->is_ajax_request()) {
			$buscar = $this->input->post('buscar_usuario');
			$num_pagina = $this->input->post('pagina_usuario');
			$cantidad = 5;
            $inicio = ($num_pagina - 1) * $cantidad;
            $data = array(
            	'usuario' => $this->Admin_model->mostrar($buscar, $inicio, $cantidad, 'usuario'),
            	'total_registros' => count($this->Admin_model->mostrar($buscar)),
            	'cantidad' => $cantidad	
            	);
			echo json_encode($data);
			exit();
		}
		else{
			show_404();
		}
	}

	function gestionarContenido(){
		if ($this->input->is_ajax_request()) {
			$buscar = $this->input->post('buscar_contenido');
			$num_pagina = $this->input->post('pagina_contenido');
            $cantidad = 5;
            $inicio = ($num_pagina - 1) * $cantidad;
            $data = array(
            	'contenido' => $this->Admin_Model->mostrarContenido($buscar, $inicio, $cantidad),
            	'total_registros' => count($this->Admin_model->mostrarContenidoCategorias($buscar)),
            	'cantidad' => $cantidad	
            	);
			echo json_encode($data);
			exit();
		}
		else{
			show_404();
		}
	}

	function gestionarAutores(){
		if ($this->input->is_ajax_request()) {
            $buscar = $this->input->post('buscar_autor');
            $num_pagina = $this->input->post('pagina_autor');
            $cantidad = 5;
            $inicio = ($num_pagina - 1) * $cantidad;
            $data = array(
            		'autor' => $this->Admin_model->mostrarAutores($buscar, $inicio, $cantidad),
            		'total_registros' => count($this->Admin_model->mostrarAutores($buscar)),
            		'cantidad' => $cantidad	
            	);
			echo json_encode($data);
			exit();
		}
		else{
			show_404();
		}
	}

	function gestionarCategorias(){
		if ($this->input->is_ajax_request()) {
            $buscar = $this->input->post('buscar_categoria');
            $num_pagina = $this->input->post('pagina_categoria');
            $cantidad = 5;
            $inicio = ($num_pagina - 1) * $cantidad;
            $data = array(
            		'categoria' => $this->Admin_model->mostrarCategorias($buscar, $inicio, $cantidad),
            		'total_registros' => count($this->Admin_model->mostrarCategorias($buscar)),
            		'cantidad' => $cantidad	
            	);
			echo json_encode($data);
			exit();
		}
		else{
			show_404();
		}
	}

	//Alta de Registro
	function agregarUsuario(){
		if ($this->input->is_ajax_request()) {
			//header('Content-type: application/json; charset=utf-8');
            if($this->input->post()){
                $tipo =  $this->input->post('tipo', TRUE);
                $membresia = $this->input->post('membresia', TRUE);
                $datos = array(
                    'id' => $this->input->post('idUsuario', TRUE),
                    'tipo' => $this->input->post('tipo', TRUE),
                    'nombre' => $this->input->post('nombre', TRUE),
                    'apellido' => $this->input->post('apellido', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'estatus' => 0,
                    'contrasena' => sha1($this->input->post('pass', TRUE)),
                    'fecha_registro' => date('Y-m-d'),
                );

                switch ($tipo) {
                    case '1':
                    break;
                    case '2':
                    break;
                    case '3':
                        $data['pais'] = $this->input->post('pais');
                        $data['membresia'] = $membresia;
                        switch ($membresia) {
                        	case '1':
                        		$data['vida'] = date('Y-m-d', strtotime("+1 year"));
                        	break;
                        	case '2':
                        		$data['vida'] = date('Y-m-d', strtotime("+5 days"));
                        	break;
                        	case '3':
                        		$data['vida'] = date('Y-m-d', strtotime("+3 month"));
                        	break;
                        }                        
                        if($this->Admin_Model->guardarUsuario($datos, $data) == FALSE){
                        	$response = array('error' => "error");
                        	echo json_encode($response);
                        }
                        else{
                        	$response = array('exito' => "exito");
                        	echo json_encode($response);
                        }
                    break;
                }
            }
		}
		else{
			show_404();
		}
	}

	function agregarContenido(){
		if ($this->input->is_ajax_request()) {
			$titulo = $this->input->post('titulo', TRUE);
			$url = convert_accented_characters(url_title($titulo,'-',TRUE));
			
			$data['tipo'] =  $this->input->post('tipo', TRUE);
			$data['enlace'] = $this->input->post('enlace', TRUE);
			$data['anio'] = $this->input->post('anio', TRUE);

            switch ($data['tipo']) {
				//Videos
				case '4':
                    $datos = array(
                        'titulo' => $titulo,
                        'estatus' => 0,
                        'fecha' => date('Y-m-d'),
                        'url' => $url,
                        //'descripcion' => $this->input->post('descripcion', TRUE),
                        'autor_id' => $this->input->post('autor', TRUE),
                        'categoria_id' => $this->input->post('categoria', TRUE),
                        'portada_id' => $this->input->post('portada', TRUE)
                    );
                    $query = $this->Admin_model->guardarContenido($datos, $data);
                    if($query == TRUE) {
						echo json_encode($query);
						exit();
					} else{
						echo json_encode($query);
						exit();
					}
				break;
				//Apps, Libros y Revistas
				default:
                    $datos = array(
                        'titulo' => $titulo,
                        'estatus' => 0,
                        'fecha' => date('Y-m-d'),
                        'url' => $url,
                        //'descripcion' => $this->input->post('descripcion', TRUE),
                        'autor_id' => $this->input->post('autor', TRUE),
                        'categoria_id' => $this->input->post('categoria', TRUE),
                    );
				    $query = $this->Admin_model->guardarContenido($datos, $data);
				    if($query == TRUE) {
						echo json_encode($query);
						exit();
					} else{
						echo json_encode($query);
						exit();
					}
				break;
			}
        }
		else{
			show_404();
		}
	}

	function agregarAutor() {
		if ($this->input->is_ajax_request()) {
			$datos = array('autor' => $this->input->post('autor', TRUE));
			$query = $this->Admin_model->guardarRegistro($datos, 'autor');
			if($query == TRUE) {
				echo json_encode($query);
				exit();
			} else{
				echo json_encode($query);
				exit();
			}
		} else {
			show_404();
		}
	}

	function agregarCategoria(){
		if ($this->input->is_ajax_request()) {
			$datos = array('categoria' => $this->input->post('categoria', TRUE));
			$query = $this->Admin_model->guardarRegistro($datos, 'categoria');
			if($query == TRUE) {
				echo json_encode($query);
				exit();
			} else{
				echo json_encode($query);
				exit();
        	}
		}
		else{
			show_404();
		}
	}

	function agregarPortada(){
		if ($this->input->is_ajax_request()) {
				$this->load->library('upload');

                $config['upload_path'] = './uploads/cover/';
                $config['file_path'] = 'uploads/cover/';
                $config['thumbnail_path'] = 'uploads/cover/thumbs/';
                $config['allowed_types'] = 'jpg|png';
                $config['encrypt_name'] = 'TRUE';
                $config['max_size'] = '2000';
                $config['max_width'] = '2024';
                $config['max_height'] = '2008';

                $this->upload->initialize($config);
                if (!$this->upload->do_upload('portada')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->layout->view('admin', $error);
                } else {
                    $file_info = $this->upload->data();
                    //Pasa el nombre de la imagen a la función create_thumbnail
                    $this->_crear_thumbnail($file_info['file_name'], 4);
                    $data = array('upload_data' => $this->upload->data());
                    $thumbnail_path = $config['thumbnail_path'].$file_info['raw_name'].'_thumb'.$file_info['file_ext'];
                    $imagen = $file_info['file_name'];
                    $nombre = $file_info['raw_name'];
                            
                    $datos = array('tipo' => 4,
                        'nombre' => $nombre,
                        'ruta' => $config['file_path'].$imagen,
                        'miniatura' => $thumbnail_path
                    );
				$this->Admin_model->guardarRegistro($datos, 'portada');
			}
        }
		else{
			show_404();
		}
	}

	function agregarSlide() {  
    	if ($this->input->is_ajax_request()) {
            if($this->input->post()){
                $tipo = $this->input->post('slides', TRUE);
                $this->load->library('upload');
                switch ($tipo) {
                    case '1': 	      
                        $config['upload_path'] = './uploads/slide/';
                        $config['file_path'] = 'uploads/slide/';
                        $config['thumbnail_path'] = 'uploads/slide/thumbs/';
                        $config['allowed_types'] = 'jpg|png';
                        $config['max_size'] = '2000';
                        $config['max_width'] = '2024';
                        $config['max_height'] = '2008';
                        break;
                    case '2':    	      
                        $config['upload_path'] = './uploads/300x300/';
                        $config['file_path'] = 'uploads/300x300/';
                        $config['thumbnail_path'] = 'uploads/300x300/thumbs/';
                        $config['allowed_types'] = 'jpg|png';
                        $config['max_size'] = '2000';
                        $config['max_width'] = '2024';
                        $config['max_height'] = '2008';
                        break;
                    case '3':  	      
                        $config['upload_path'] = './uploads/1000x150/';
                        $config['file_path'] = 'uploads/1000x150/';
                        $config['thumbnail_path'] = 'uploads/1000x150/thumbs/';
                        $config['allowed_types'] = 'jpg|png';
                        $config['max_size'] = '2000';
                        $config['max_width'] = '2024';
                        $config['max_height'] = '2008';
                        break;
                }
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('ruta')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->layout->view('admin', $error);
                } else {
                    $file_info = $this->upload->data();
                    //Pasa el nombre de la imagen a la función create_thumbnail
                    $this->_crear_thumbnail($file_info['file_name'], $tipo);
                    //$data = array('upload_data' => $this->upload->data());
                    $imagen = ucfirst($file_info['raw_name']);
                    $ruta = $file_info['file_name'];
                    $thumbnail_path = $config['thumbnail_path'].$file_info['raw_name'].'_thumb'.$file_info['file_ext'];

                    $datos = array('tipo' => $tipo,
                                   'nombre' => $imagen,
                                   'ruta' => $config['file_path'].$ruta,
                                   'miniatura' => $thumbnail_path,
                                   'estatus' => 0
	                );
	                $this->Admin_model->guardarRegistro($datos, 'slides');
                }
            }
        }
	    else {
	      	show_404();
	    }
    }

	//Obtener Datos de Registo a Actualizar
	function editarUsuario(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idUsuario');
			$query = $this->Admin_model->getUsuario($id);
			$datos = array(
                0 => $query->tipo,
                1 => $query->idMembresia,
            	2 => $query->nombre,
            	3 => $query->apellido,
                4 => $query->idPais,
				5 => $query->email,
                6 => $query->contrasena
			);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
	}

	function editarContenido(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idCatalogo');
			$query = $this->Admin_model->getContenido($id);
			$datos = array(
                0 => $query->tipo,
                1 => $query->titulo,
				2 => $query->idAutor,
            	3 => $query->idCategoria,
				4 => $query->idPortada,
                5 => $query->anio,
                6 => $query->enlace,
                //7 => $query->descripcion
			);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
	}

	function editarAutor(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idAutor');
			$query = $this->Admin_model->getRegistro($id, 'autor');
			$datos = array(
            	0 => $query->autor,
			);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
    }

    function editarCategoria(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idCategoria');
			$query = $this->Admin_model->getRegistro($id, 'categoria');
			$datos = array(
            	0 => $query->categoria,
			);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
    }

    function editarNoticia(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idNoticias');
			$query = $this->Admin_model->getNoticia($id);
			$datos = array(
				0 => $query->tipo_noticia_id,
                1 => $query->titulo,
				2 => $query->fecha,
                3 => $query->contenido
			);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
	}

	//Actualizar Registro
	function actualizarUsuario(){
		if ($this->input->is_ajax_request()) {
            $tipo = $this->input->post('tipo', TRUE);
			$datos = array(
				'id' => $this->input->post('idUsuario', TRUE),
                'nombre' => $this->input->post('nombre', TRUE),
                'apellido' => $this->input->post('apellido', TRUE),
                'email' => $this->input->post('email', TRUE),
                'contrasena' => sha1($this->input->post('pass', TRUE))
            );

            switch ($tipo) {
					case '1':
					break;
					case '2':
					break;
					case '3':
                        $data['pais'] = $this->input->post('pais');
                        $data['membresia'] = $this->input->post('membresia');
                        $this->Admin_Model->actualizarUsuario($datos, $data);
					break;
            }
		}
		else{
			show_404();
		}
	}

	function actualizarContenido(){
		if ($this->input->is_ajax_request()) {
			$titulo = $this->input->post('titulo', TRUE);
			$url = convert_accented_characters(url_title($titulo,'-',TRUE));
			
			$data['tipo'] =  $this->input->post('tipo', TRUE);
			$data['enlace'] = $this->input->post('enlace', TRUE);
			$data['anio'] = $this->input->post('anio', TRUE);

            switch ($data['tipo']) {
				//Videos
				case '4':
                    $datos = array(
                        'id' => $this->input->post('idCatalogo', TRUE),
                        'titulo' => $titulo,
                        'url' => $url,
                        //'descripcion' => $this->input->post('descripcion', TRUE),
                        'autor_id' => $this->input->post('autor', TRUE),
                        'categoria_id' => $this->input->post('categoria', TRUE),
                        'portada_id' => $this->input->post('portada', TRUE)
                    );
                    $query = $this->Admin_model->actualizarContenido($datos, $data);
                    if($query == TRUE) {
						echo json_encode($query);
						exit();
					} else{
						echo json_encode($query);
						exit();
					}
				break;
				//Apps, Libros y Revistas
				default:
                    $datos = array(
                        'id' => $this->input->post('idCatalogo', TRUE),
                        'titulo' => $titulo,
                        'url' => $url,
                        //'descripcion' => $this->input->post('descripcion', TRUE),
                        'autor_id' => $this->input->post('autor', TRUE),
                        'categoria_id' => $this->input->post('categoria', TRUE),
                    );
				    $query = $this->Admin_Model->actualizarContenido($datos, $data);
				    if($query == TRUE) {
						echo json_encode($query);
						exit();
					} else{
						echo json_encode($query);
						exit();
					}
				break;
			}
		}
		else{
			show_404();
		}
	}

	function actualizarAutor(){
		if ($this->input->is_ajax_request()) {
            $datos = array(
                'id' => $this->input->post('idAutor', TRUE),
                'autor' => $this->input->post('autor', TRUE)
            );
			$query = $this->Admin_model->actualizarRegistro($datos, 'autor');
			if($query == TRUE) {
				echo json_encode($query);
				exit();
			} else{
				echo json_encode($query);
				exit();
        	}
        } else{
			show_404();
		}
	}

	function actualizarCategoria(){
		if ($this->input->is_ajax_request()) {
            $datos = array(
                'id' => $this->input->post('idCategoria', TRUE),
                'categoria' => $this->input->post('categoria', TRUE)
            );
			$query = $this->Admin_model->actualizarRegistro($datos, 'categoria');
			if($query == TRUE) {
				echo json_encode($query);
				exit();
			} else{
				echo json_encode($query);
				exit();
        	}
        }
		else{
			show_404();
		}
	}

	function actualizarNoticia(){
		if ($this->input->is_ajax_request()) {
            $titulo = $this->input->post('titulo', TRUE);
			$url = convert_accented_characters(url_title($titulo,'-',TRUE));
            $datos = array(
                'id' => $this->input->post('idNoticias', TRUE),
                'tipo_noticia_id' => $this->input->post('tipo', TRUE),
                'titulo' => $titulo,
                'contenido' => $this->input->post('contenido', TRUE),
                'url' => $url,
                'fecha' => $this->input->post('fecha', TRUE),
			);
            $this->Admin_model->actualizarRegistro($datos, 'noticias');
		}
		else{
			show_404();
		}
	}

	//Eliminar Registro
	function eliminarUsuario(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idUsuario', TRUE);
			$this->Admin_model->eliminarUsuario($id);
		}
		else{
			show_404();
		}
	}

	function eliminarContenido(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idCatalogo', TRUE);
			$query = $this->Admin_model->eliminarContenido($id);
			if($query == TRUE) {
				echo json_encode($query);
				exit();
			} else{
				echo json_encode($query);
				exit();
			}
		}
		else{
			show_404();
		}
	}

	function eliminarAutor(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idAutor', TRUE);
			$query = $this->Admin_model->eliminarRegistro($id, 'autor');
			if($query == TRUE) {
				echo json_encode($query);
				exit();
			} else{
				echo json_encode($query);
				exit();
        	}
		}
		else{
			show_404();
		}
	}

	function eliminarCategoria(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idCategoria', TRUE);
			$query = $this->Admin_model->eliminarRegistro($id, 'categoria');
			if($query == TRUE) {
				echo json_encode($query);
				exit();
			} else{
				echo json_encode($query);
				exit();
        	}
		}
		else{
			show_404();
		}
	}

	function eliminarPortada(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idPortada', TRUE);
            $datos['file'] = $this->Admin_model->getRegistro($id, 'portada');
            $accion = $this->borrarPortada($id);
            if($accion == FALSE and empty($datos)){
	            $datos['exito'] = "Incorrecto!!";
            	$this->layout->view('error', $datos);
	        }
	        else{
	           	rename($datos['file']->ruta, $datos['file']->ruta.'_delete');
	           	rename($datos['file']->miniatura, $datos['file']->miniatura.'_delete');
	        }
		}
		else{
			show_404();
		}
	}

	function eliminarSlide(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idSlides', TRUE);
            $datos['file'] = $this->Admin_model->getRegistro($id, 'slides');
            if(empty($datos)){
	            $datos['exito'] = "Incorrecto!!";
            	$this->layout->view('error', $datos);
	        }
	        else{
	           	rename($datos['file']->ruta, $datos['file']->ruta.'_delete');
	           	rename($datos['file']->miniatura, $datos['file']->miniatura.'_delete');
            	$this->borrarSlide($id);
	        }
		}
		else{
			show_404();
		}
	}

	function eliminarNoticia(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idNoticias', TRUE);
			$this->Admin_model->eliminarRegistro($id, 'noticias');
		}
		else{
			show_404();
		}
	}

	//Cambiar Status a Registro 
	function estatusUsuario(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idUsuario', TRUE);
			$datos = $this->Admin_model->getUsuario($id);
			$estatus = ($datos->estatus == 1) ? 0 : 1;
			$this->Admin_model->cambiarStatus($id, $estatus, 'usuario');
		}
		else{
			show_404();
		}
	}

	function estatusContenido(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idCatalogo', TRUE);
			$datos = $this->Admin_model->getContenido($id);
			if($datos->estatus == 1){
				$estatus = 0;
			}
			else{
				$estatus = 1;
			}
			$this->Admin_Model->cambiarStatus($id, $estatus);
		}
		else{
			show_404();
		}
	}

	function estatusSlide(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idSlides', TRUE);
			$datos = $this->Admin_model->getRegistro($id, 'slides');
			if($datos->estatus == 1){
				$estatus = 0;
			}
			else{
				$estatus = 1;
			}
			$this->Admin_Model->cambiarStatus($id, $estatus);
		}
		else{
			show_404();
		}
    }

    function estatusNoticia(){
		if ($this->input->is_ajax_request()) {
			$id = $this->input->post('idNoticias', TRUE);
			$datos = $this->Admin_model->getRegistro($id, 'noticias');
			if($datos->estatus == 1){
				$estatus = 0;
			}
			else{
				$estatus = 1;
			}
			$this->Admin_model->estatusNoticia($id, $estatus, 'noticias');
		}
		else{
			show_404();
		}
    }

    //Listar Datos	
	function listarAutor(){
		if ($this->input->is_ajax_request()) {
			$autor = $this->Admin_model->mostrarAutores();
			echo json_encode($autor);
			exit();
		}
		else{
			show_404();
		}
	}

	function listarCategoria(){
		if ($this->input->is_ajax_request()) {
			$categoria = $this->Admin_model->listarDatos('categoria');
			echo json_encode($categoria);
			exit();
		}
		else{
			show_404();
		}
	}

    function listarPortada(){
		if ($this->input->is_ajax_request()) {
			$portada = $this->Admin_model->mostrarPortada();
			echo json_encode($portada);
			exit();
		}
		else{
			show_404();
		}
	}

    function listarSlides(){
		if ($this->input->is_ajax_request()) {
			$tipo = $this->input->post('tipo', TRUE);
			$datos = $this->Admin_model->mostrarSlides($tipo);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
	}

	function listarNoticias(){
		if ($this->input->is_ajax_request()) {
			$buscar = $this->input->post("buscar_noticia");
			$datos = $this->Admin_model->mostrarNoticias($buscar);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
	}

//Otras Funciones
    //Generar códigos
    function generarCodigos(){
		if ($this->input->is_ajax_request()) {
            $cantidad = $this->input->post('cantidad', TRUE);
            for ($i = 1; $i <= $cantidad; $i++) {
                $codGenerado = $this->randomCodigo(20);
                $datos = array(
                    'codigo' => $codGenerado,
                    'estatus' => 0
                );
                $this->Admin_Model->guardarCodigos($datos);
            }
		}
		else{
			show_404();
		}
	}
    
    function randomCodigo($longitud) { 
        $codigo = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $longitud);
        return $codigo;
    } 
    
	//Filtrar Contenido
	function filtrarContenido(){
		if ($this->input->is_ajax_request()) {
			$filtro = $this->input->post('filtro');
			$datos = $this->Admin_model->filtrarContenido($filtro);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
	}

	function filtrarNoticias(){
		if ($this->input->is_ajax_request()) {
			$filtro = $this->input->post("filtro");
			$datos = $this->Admin_model->filtrarNoticias($filtro);
			echo json_encode($datos);
			exit();
		}
		else{
			show_404();
		}
	}
    
 	//Elimina imágenes
    function borrarSlide($id){   
        if(empty($id)){
            return FALSE;
        }
        else{
            $this->Admin_model->eliminarRegistro($id, 'slides');
            return TRUE;
        }
    }

    function borrarPortada($id){   
        if(empty($id)){
            return FALSE;
        }
        else{
            $this->Admin_Model->eliminarRegistro($id, 'portada');
            return TRUE;
        }
    }
    
    //Crear thumbnail
    function _crear_thumbnail($filename, $tipo){
        switch ($tipo) {
        	case '1':
        		$config['image_library'] = 'gd2';
		        //Ubica la imágen a redimensionar
		        $config['source_image'] = 'uploads/slide/'.$filename;
		        $config['create_thumb'] = TRUE;
		        $config['maintain_ratio'] = TRUE;
		        //Guardamos la miniatura
		        $config['new_image'] = 'uploads/slide/thumbs/';
		        $config['width'] = 150;
		        $config['height'] = 150;
        		break;
        	case '2':
        		$config['image_library'] = 'gd2';
		        //Ubica la imágen a redimensionar
		        $config['source_image'] = 'uploads/300x300/'.$filename;
		        $config['create_thumb'] = TRUE;
		        $config['maintain_ratio'] = TRUE;
		        //Guardamos la miniatura
		        $config['new_image'] = 'uploads/300x300/thumbs/';
		        $config['width'] = 150;
		        $config['height'] = 150;
        		break;
        	case '3':
        		$config['image_library'] = 'gd2';
		        //Ubica la imágen a redimensionar
		        $config['source_image'] = 'uploads/1000x150/'.$filename;
		        $config['create_thumb'] = TRUE;
		        $config['maintain_ratio'] = TRUE;
		        //Guardamos la miniatura
		        $config['new_image'] = 'uploads/1000x150/thumbs/';
		        $config['width'] = 150;
		        $config['height'] = 150;
        		break;
            case '4':
        		$config['image_library'] = 'gd2';
		        //Ubica la imágen a redimensionar
		        $config['source_image'] = 'uploads/cover/'.$filename;
		        $config['create_thumb'] = TRUE;
		        $config['maintain_ratio'] = TRUE;
		        //Guardamos la miniatura
		        $config['new_image'] = 'uploads/cover/thumbs/';
		        $config['width'] = 150;
		        $config['height'] = 150;
        		break;
        }
        $this->load->library('image_lib', $config); 
        $this->image_lib->resize();
    }
	
}