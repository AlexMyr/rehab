<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/
 
	$page_access = array (
                          'video' => array(
									'perm' => 4,
									'module' => 'page',
									),
      
						'redirect' => array(
									'perm' => 4,
//									'menu' => 'unloged'
									),                                        
						'cms' => array(
									'perm' => 4,
									),
						'faq' => array(
									'perm' => 4,
									'module' => 'faq',
									),
                        'contact_thankyou2' => array(
									'perm' => 4,
									'module' => 'page',
									),
						'login' => array(
									'perm' => 4,
									'module' => 'page',
									'session' => 1
									),
						'forgotpass' => array(
									'perm' => 4,
									'module' => 'page',
									),
//--------------------------- Logged in Member page access -----------------------------
                        'programs' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
                        'program_update_exercise' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
                        'program_preview_exercise' => array(
									'perm' => 3,
									'module' => 'exercise_preview',
									'session' => 1
									),
                        'program_add_patient' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),

						'dashboard' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),                 
									
						'profile' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
						'profile_payment' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),               
						'profile_do_payment' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),               
						'profile_choose_clinic' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),                 
						'profile_exercise_add' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),                 
						'profile_edit' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),                 
						'profile_edit_email' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
						'profile_edit_password' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
						'profile_header_paper' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),

						'profile_licence' => array(
									'perm' => 2,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
									              
						'client' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
						'client_add' => array(
									'perm' => '3',
									'module' => 'trainer_dashboard',
									'session' => 1
									),
						'client_delete' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),        
						'client_add_exercise' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),                 
						'client_update_exercise' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
						'client_preview_exercise' => array(
									'perm' => 3,
									'module' => 'exercise_preview',
									'session' => 1
									),
						'client_email' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
//--------------------------- Logged in Member AJAX -----------------------------
						'xgetexercise' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
                        'pgetexercise' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
                        'getclients' => array(
									'perm' => 3,
									'module' => 'trainer_dashboard',
									'session' => 1
									),
//--------------------------- Logged in Member PDF -----------------------------
						'exercisepdf' => array(
									'perm' => 3,
									'folder' => 'pdf',
									'session' => 1
									),
                        'pexercisepdf' => array(
									'perm' => 3,
									'folder' => 'pdf',
									'session' => 1
									),
						'printpdf' => array(
									'perm' => 3,
									'module' => 'exercise_preview',
									'folder' => 'pdf',
									'session' => 1
									),                 
						);

/*
	$menu_access = array (
						'unloged' => 4,
						'logged' => 3,
						);
*/
						
	// Site Module array. Only for CMS combined projects
	$site_module = array (
						'trainer_dashboard' => array(
												'template_file' => 'main_dashboard.html'
												),
						'page' => array(
									'template_file' => 'main_template.html'
										),
						'faq' => array(
									'template_file' => 'faq_main_template.html'
										),
						'exercise_preview' => array(
												'template_file' => 'exercise_preview.html'
												),
						);

?>