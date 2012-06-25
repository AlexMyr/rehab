<?php
/*************************************************************************
* @Author: Tinu Coman                                          			 *
*************************************************************************/

$func_access = array 
				( 
					'contact' => array(
										'add'=> 4,
									),
					'auth' => array(
                                    'login_fb' => 4,
									'login' => 4,
									'logout' => 4,
								),
					'member' => array(
										'join_now' => 4,
										'forgotpass' => 4,
										'add_profile' => 3,
										'update_licence' => 3,
										'update_profile' => 3,
										'update_profile_notes' =>3,
										'delete_profile' => 3,
										'update_email' => 3,
										'update_pass' => 3,
										'update_custom_header' => 3,
										'pay' => 3,
										'cancel_payment' => 3,
                                        'confirm_pay' => 3,
                                        'ipn_confirm' => 3,
                                        'exercise_add' => 3,
                                        'exercise_update' => 3,
                                        'exercise_delete' => 3,
//										'payment' => 3,
									),
					'client' => array(
											'add_client' => 3,
                                            'add_program_plan' => 3,
                                            'update_program_exercise' => 3,
                                            'update_program_exercise_plan' => 3,
                                            'send_program_email' => 3,
											'update_client' => 3,
											'delete_client' => 3,
											'add_exercise' => 3,
											'mail_exercise' => 3,
											'print_exercise' => 3,
											'pdf_exercise' => 3,
											'update_exercise' => 3,
											'update_exercise_plan' => 3,
											'delete_exercise' => 3,
                                            'delete_program_plan' => 3,
										),
				);
 
?>