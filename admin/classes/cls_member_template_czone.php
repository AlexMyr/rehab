<?php

/************************************************************************

* @Author: Tinu Coman

***********************************************************************/

class member_template_czone

{

  var $dbu;



function member_template_czone()

{

    $this->dbu=new mysql_db;

}



/****************************************************************

* function add(&$ld)                                            *

****************************************************************/



function add(&$ld)

{

	$db=new mysql_db;

	if(!$this->add_validate($ld))

	{

		return false;

	}

                

	$ld['member_czone_id']=$this->dbu->query_get_id("insert into trainer_dashboard_template_czone (

                                                                       mode,

                                                                       name,

                                                                       tag,

                                                                       content

                                                                        )

                                                                        values

                                                                        (

                                                                        '".$ld['mode']."',

                                                                        '".$ld['name']."',

                                                                        '".$ld['tag']."',

                                                                        '".$ld['content']."'

                                                                        )

                                                                       ");

	$ld['error']="Member Template Content Zone Succesfully added.";

    return true;

}



/****************************************************************

* function update(&$ld)                                         *

****************************************************************/

function update(&$ld)

{

	if(!$this->update_validate($ld))

	{

		return false;

	}

       

    $this->dbu->query("update trainer_dashboard_template_czone set

                       mode='".$ld['mode']."',

                       name='".$ld['name']."',

                       tag='".$ld['tag']."',

                       content='".$ld['content']."'

                       where

                       template_czone_id='".$ld['template_czone_id']."'"

                      );

                      

    $ld['error'].="Member Template Content Zone successfully updated.";

    return true;

}



/****************************************************************

* function delete(&$ld)                                         *

****************************************************************/

function delete(&$ld)

{

		if(!$this->delete_validate($ld))

        {

                return false;

        }

        

        $this->dbu->query("delete from trainer_dashboard_template_czone where template_czone_id='".$ld['template_czone_id']."'");



        $ld['error'].="Member Template Content Zone has been successfully deleted.";

        return true;

}        



/****************************************************************

* function add_validate(&$ld)                                   *

****************************************************************/



function add_validate(&$ld)

{

    $is_ok=true;

    if (!is_numeric($ld['mode']))

    {

        return false;

    }



    if(!$ld['name'])

    {

        $ld['error'].="Please fill in the Name field."."<br>";

        $is_ok=false;

    }

    

    if(!$ld['tag'])

    {

        $ld['error'].="Please fill in the Tag field."."<br>";

        $is_ok=false;

    }

    elseif (!secure_string_no_spaces($ld['tag']))

    {

        $ld['error'].="Please fill in the Tag field with valid data (no spaces allowed)."."<br>";

        $is_ok=false;

    }



    return $is_ok;

}





/****************************************************************

* function update_validate(&$ld)                                *

****************************************************************/

function update_validate(&$ld)

{

        $is_ok=true;

        if (!is_numeric($ld['mode']))

        {

            return false;

        }

        if (!is_numeric($ld['template_czone_id']))

        {

            $ld['error'].="Invalid ID.<br>";

            return false;

        }

        $this->dbu->query("select template_czone_id from trainer_dashboard_template_czone where template_czone_id='".$ld['template_czone_id']."'");

        if(!$this->dbu->move_next())

        {

            $ld['error'].="Invalid ID.<br>";

            return false;

        }

        return $this->add_validate($ld);

}





/****************************************************************

* function delete_validate(&$ld)                                *

****************************************************************/

function  delete_validate(&$ld)

{

		$is_ok=true;

        if (!is_numeric($ld['template_czone_id']))

        {

            $ld['error'].="Invalid ID.<br>";

            return false;

        }

        $this->dbu->query("select template_czone_id from trainer_dashboard_template_czone where template_czone_id='".$ld['template_czone_id']."'");

        if(!$this->dbu->move_next())

        {

            $ld['error'].="Invalid ID.<br>";

            return false;

        }



        return $is_ok;

}



}//end class

?>



