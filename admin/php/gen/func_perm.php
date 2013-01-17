<?php

/*************************************************************************

* @Author: Tinu Coman                                          			 *

*************************************************************************/

   $func_access=array (     'auth' => array(

                                         'login'=>4,

                                         'logout'=>4

                                        ),

                                        

                            'template' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        'czone_add'=>1,

                                        'czone_update'=>1,

                                        'czone_delete'=>1,

                                        ),

                                        

                            'content_template' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1

                                        ),

                            'menu_template_file' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1

                                        ),

                                        

                            'menu' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        'h_version_remove'=>1,

                                        'v_version_remove'=>1,

                                        'h_version_update'=>1,

                                        'v_version_update'=>1,

                                        ),

                                        

                            'page_type' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        'czone_update'=>1,

                                        'czone_empty'=>1,

                                        ),

                                        

                            'web_page' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        'set_home'=>1,

                                        ),

                                        

                            'web_page_content' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        'sort_order_update'=>1,

                                        ),

                                        

                            'cbox' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        ),

                                        

                            'tag' => array(

                                        'action_box_add'=>1,

                                        'action_box_update'=>1,

                                        'action_box_delete'=>1,

                                        ),

                                        

                            'menu_link' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'sort_order_update'=>1,

                                        'delete'=>1

                                        ),

                                        

                           'faq_category' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'activate'=>1,

                                        'deactivate'=>1,

                                        'sort_order_update'=>1,

                                        'delete'=>1

                                        ),

                       'classifieds_category' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'activate'=>1,

                                        'deactivate'=>1,

                                        'sort_order_update'=>1,

                                        'delete'=>1

                                        ),

                                        

                            'faq' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'sort_order_update'=>1,

                                        'delete'=>1

                                        ), 

                                        

                            'settings' => array(

                                         'admin_update'=>1

                                         ),

                                    

                            'programs' => array(

                                        'add'=>1,

                                        'add_mult'=>1,
                                        
                                        'update'=>1,

                                        'delete'=>1,

                                        'upload_file'=>1,

                                        'resize'=>1,

                                        'delete_file'=>1,

                                 				'programs_category_add'		=> 1,

                                        		'programs_category_delete'	=> 1,
                                        
                                        'change_sort_order'=>1,

                                        ),

                                        

                            'programs_category' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        'activate'=>1,

                                        'deactivate'=>1,

                                        ),

                                        

/************** Member ***********/	

							'member' => array(

										'add'=>1,

										'delete'=>1,

										'update'=>1,

										'deactivate'=>1,

										'activate'=>1,
										
										'activate_full_rights'=>1,
                                        
                                        'trial'=>1,
                                        'extend_trial' => 1

										),                                    

							'member_template_czone' => array(

                                        'add'=>1,

                                        'update'=>1,

                                        'delete'=>1,

                                        ),

							'sys_message' => array(

										'add' =>1,

										'update' =>1,

										),

							'price' => array(

										'add' =>1,

										'update' =>1,

										),
                            'translation' => array(
                                        'list' => 1,
                                        'update' => 1,
                                        'meta_list' => 1,
                                        'meta_update' => 1
                                        ),
                            'exercise' => array(
                                        'list' => 1,
                                        'update' => 1
                                        )

);

 

?>