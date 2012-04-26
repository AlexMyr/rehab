<?php
/************************************************************************
* @Author: Tinu Coman
***********************************************************************/
class price
{
  var $dbu;

function price()
{
    $this->dbu=new mysql_db;
}
/****************************************************************
* function add(&$ld)                                            *
****************************************************************/

function add(&$ld)
{

	if(!$this->add_validate($ld))
	{
		return false;
	}
	$ld['price_id']=$this->dbu->query_get_id("insert into price_plan_new (
                                                                      price_plan_name,
                                                                      licence_amount,
                                                                      licence_period,
                                                                      price_value,
																	  has_logo,
																	  can_create_exercise,
																	  email,
																	  photo_lineart
                                                                        )
                                                                        values
                                                                        (
                                                                        '".$ld['price_plan_name']."',
                                                                        '".$ld['licence_amount']."',
                                                                        '".$ld['licence_period']."',
                                                                        '".$ld['price_value']."',
																		'".(isset($ld['has_logo']) ? 1 : 0)."',
																		'".(isset($ld['can_create_exercise']) ? 1 : 0)."',
																		'".(isset($ld['email']) ? 1 : 0)."',
																		'".(isset($ld['photo_lineart']) ? 1 : 0)."'
                                                                        )
                                                                       ");
   	$ld['error']="Price Succesfylly added.";
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
	
       
    $this->dbu->query("update price_plan_new set
						price_plan_name='".$ld['price_plan_name']."',
						licence_amount='".$ld['licence_amount']."',
						licence_period='".$ld['licence_period']."',
						currency='".$ld['currency']."',
						price_value='".$ld['price_value']."',
						has_logo='".(isset($ld['has_logo']) ? 1 : 0)."',
						can_create_exercise='".(isset($ld['can_create_exercise']) ? 1 : 0)."',
						email='".(isset($ld['email']) ? 1 : 0)."',
						photo_lineart='".(isset($ld['photo_lineart']) ? 1 : 0)."'
    					where price_id='".$ld['price_id']."'"
                      );                      
    $ld['error'].="Price successfully updated.";
    return true;
}
/****************************************************************
* function add_validate(&$ld)                                   *
****************************************************************/

function add_validate(&$ld)
{
    $is_ok=true;

    if(!$ld['price_plan_name'])
    {
        $ld['error'].="Please fill in the Licence Name Field."."<br>";
        $is_ok=false;
    }
    if(!$ld['licence_amount'])
    {
        $ld['error'].="Please fill in the Licence Amount Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['licence_period'])
    {
        $ld['error'].="Please fill in the Licence Period Field."."<br>";
        $is_ok=false;
    }
/*    
    if(!$ld['currency'])
    {
        $ld['error'].="Please fill in the Currency Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['price_value'])
    {
        $ld['error'].="Please fill in the Price Field."."<br>";
        $is_ok=false;
    }
*/
    return $is_ok;
}


/****************************************************************
* function update_validate(&$ld)                                *
****************************************************************/
function update_validate(&$ld)
{
    $is_ok=true;
    if (!is_numeric($ld['price_id']))
    {
        $ld['error'].="Invalid Price ID.<br>";
        return false;
    }
    $this->dbu->query("select price_id from price_plan where price_id='".$ld['price_id']."'");
    if(!$this->dbu->move_next())
    {
        $ld['error'].="Invalid Price ID.<br>";
        return false;
    }
    
    if(!$ld['price_plan_name'])
    {
        $ld['error'].="Please fill in the Licence Name Field."."<br>";
        $is_ok=false;
    }
    if(!$ld['licence_amount'])
    {
        $ld['error'].="Please fill in the Licence Amount Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['licence_period'])
    {
        $ld['error'].="Please fill in the Licence Period Field."."<br>";
        $is_ok=false;
    }
/*    
    if(!$ld['currency'])
    {
        $ld['error'].="Please fill in the Currency Field."."<br>";
        $is_ok=false;
    }
    
    if(!$ld['price_value'])
    {
        $ld['error'].="Please fill in the Price Field."."<br>";
        $is_ok=false;
    }
*/
    return $is_ok;
}

}//end class
?>

