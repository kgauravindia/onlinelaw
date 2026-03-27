<?php
require('includes/function.php');
$_POST =post_clean($_POST);
$_GET =post_clean($_GET);
if(isset($_GET['task']) )
	{
	$task =xss_clean($_GET['task']);
	
	switch($task)
		{	
			case "signup" : // Insert Data in Member and Advocate Table 
				extract($_POST);
				
				$sql ="SELECT id, name, mobile,password FROM advocate  where mobile ='$mobile' UNION ALL SELECT id, name, mobile, password  FROM member where mobile ='$mobile'";
				$res = direct_sql($sql);
				if($res['count']==0)
				{
					unset($_POST['user_type']);
					unset($_POST['tc']);
					//$password = rand(100000,999999);
					$password =rnd_str(6);
					$_POST['created_at'] =date('Y-m-d H:i:s');
					$_POST['password'] =md5($password);
					$res = insert_data($user_type,$_POST);
					if($res['id'] !=0)
					{
						//$sms = "Dear ". $name .", Your OTP is $passsword Thanks for Choosing Services Regards Regards  $inst_name";
						$sms= "Dear ". $name .", thanks for joining MyAdvIndia Your Password: ". $password ." Regards MyAdvIndia www.myadv.in";
					//	bulk_sms($mobile, $sms);
						bulk_msg($mobile, $sms);
						rtfmail($email, 'Congrats for joining', $sms);
					}
				}
				else{
					$res['msg'] ='Number Already Registered';
					$res['status'] ='error';
				}
					echo json_encode($res);
				break;
				
			case "login" :
				extract($_POST);
				$password =md5($password);
				$sql="SELECT id, name, mobile, email, 'advocate' as 'user_type' FROM advocate  where mobile ='$mobile' and password='$password' and status !='BLOCK' UNION ALL SELECT id, name, mobile, email , 'member' as 'user_type' from  member where mobile ='$mobile' and password='$password' and status !='BLOCK'";
				//$res = direct_sql("select * from $user_type where mobile ='$mobile' and password ='$password' and status !='BLOCK'");
				$res = direct_sql($sql);
				//print_r($res);
				if ($res['status']=='success' and $res['count'] ==1)
				{
					$uid = $res['data'][0]['id'];
					$user_type = $res['data'][0]['user_type'];
					$updated_at = date('Y-m-d H:i:s');
					$udata =array('status'=>'ACTIVE','updated_at'=>$updated_at,'token'=>$token);
					$result= update_data($user_type,$udata,$uid);
					if($user_type =='advocate') 
						{ 
							$result['url'] = 'advocate_dashboard';
						} 
						else{ 
							$result['url']='member_dashboard';
						}
					$_SESSION['user_type'] = $user_type;
					$_SESSION['user_id'] = $res['data'][0]['id'];
					$_SESSION['user_mobile'] = $res['data'][0]['mobile'];
					$_SESSION['user_name'] = $res['data'][0]['name'];
					$_SESSION['user_key'] = rnd_str(28);
					//setcookie("username", $_SESSION['user_name'], time()+3600, "/", "",  0);
				}
				else{
					$result['id'] =0;
					$result['status'] ='fail';
					$result['msg'] ='Invalid credentials check and login again';
				}
				echo json_encode($result);
				break;	
				
			case "master_delete" : // Delete Any Data From Table 
				extract($_POST);
				$res = delete_data($table,$id,$pkey);
				echo json_encode($res);
				break;
			
			case "send_sms" : // SMS SMS any TIME
				extract($_POST);
				$res = bulk_sms($mobile,$sms);
				echo json_encode($res);
				break;
				
			case "master_block" : // BLOCK Any Data From Table 
				extract($_POST);
				//print_r($_POST);
				$bdata =array('status'=>'BLOCK');
				$res = update_data($table,$bdata,$id,$pkey);
				echo json_encode($res);
				break;
			
			case "block_user" : // BLOCK Any Data From Table 
				extract($_POST);
				//print_r($_POST);
				$bdata =array('status'=>$data_status);
				$res2 = update_data('center_details',$bdata,$id,'center_code');
				$res = update_data('user',$bdata,$id,'user_name');
				$res['msg']  ='User and Center '.$data_status.' Successfully';
				$res['url'] = 'show_user';
				echo json_encode($res);
				break;
				
			case "update_status" : // Update Status Data From Table 
				extract($_POST);
				$st = $_POST['data_status'];
				$sid =$_POST['sid'];
				$bdata =array('status'=>$st);
				foreach($sid as $id)
				{
					if($st=='DELETE')
					{
						$res = delete_data('student',$id,'student_id');
					}
					else{
						$res = update_data('student',$bdata,$id,'student_id');
						if($st =='VERIFIED')
						{
						$data = get_data('student',$id,null,'student_id')['data'];
						$course =courseinfo($id,'course_code');
						
						$sms ="Hello ". $data['student_name'] ." Your registration for course ". $course ." in accepted. Now you can check your admission status. \n Regards \n ".$inst_name. $inst_url;
						sendsms($data['student_mobile'],$sms);
						}
					}
					
				}
				echo json_encode($res);
				break;
				
			
				
			case "login_as" :
				extract($_POST);
				
				$res = direct_sql("select * from user where user_name ='$user_name' and user_pass ='$user_pass' and status !='BLOCK'");
				
				if ($res['status']=='success' and $res['count'] ==1)
				{
					$uid = $res['data'][0]['user_id'];
					$udata =array('status'=>'ACTIVE','token'=>$token);
					$result= update_data('user',$udata,$uid,'user_id');
					$_SESSION['user_id'] = $res['data'][0]['user_id'];
					$_SESSION['user_type'] = $res['data'][0]['user_type'];
					$_SESSION['user_name'] = $res['data'][0]['user_name'];
					//setcookie("username", $_SESSION['user_name'], time()+3600, "/", "",  0);
				}
				else{
					$result['id'] =0;
					$result['status'] ='fail';
					$result['msg'] ='system is already Login';
				}
				echo json_encode($result);
				break;
				
			
			case "logout" :
				$user_id =$_SESSION['user_id'];
				$user_type =$_SESSION['user_type'];
				unset($_SESSION['user_name']);
				unset($_SESSION['user_type']);
				unset($_SESSION['user_id']);
				session_destroy();
				$updated_at = date('Y-m-d H:i:s');
				$udata =array('token'=>'','updated_at'=>$updated_at,'status'=>'LOGOUT');
				$result= update_data($user_type,$udata,$user_id);
				echo json_encode($result);
				break;
				
			case "forget_password" :
				$mobile  =$_POST['mobile'];
				
				$sql="SELECT id, name, mobile, email, 'advocate' as 'user_type' FROM advocate  where mobile ='$mobile' UNION ALL SELECT id, name, mobile, email , 'member' as 'user_type' from  member where mobile =$mobile";
				$res = direct_sql($sql);
				if($res['count'] >0)
				{
					$id = $res['data'][0]['id'];
					$user_type = $res['data'][0]['user_type'];
					$email = $res['data'][0]['email'];
					$mobile = $res['data'][0]['mobile'];
					$name = $res['data'][0]['name'];
				    //$np = rand(100000,999999);
					$np =rnd_str(6);
					$up = array('password'=>md5($np));
					$res = update_data($user_type,$up,$id,'id');
					//$sms = "Dear ". $name .", Your OTP is  $np \n Thanks for Choosing Services Regards Regards \n$inst_name "; 
					$sms = "Dear ". $name ." Your new Password: ". $np ."\nRegards \nMyAdv India \nwww.myadv.in";
					rtfmail($email,'Your password changed' ,$sms ,$noreply_email);
					//bulk_sms($mobile,$sms);
					bulk_msg($mobile,$sms); // ACTIVE
					$data['id'] =$id;
					$data['status'] ='success';
					$data['msg'] ="New Password Successfully Send to $email";
					
				}
				else{
					$data['id'] =0;
					$data['status'] ='error';
					$data['msg'] ='No any user exist with this ID. Try Again';
				}
				echo json_encode($data);
				break;
			case "email_verification" :
				$mobile  =$_POST['mobile'];
				
				$sql="SELECT id, name, mobile, email, 'advocate' as 'user_type' FROM advocate  where mobile ='$mobile' UNION ALL SELECT id, name, mobile, email , 'member' as 'user_type' from  member where mobile =$mobile";
				$res = direct_sql($sql);
				if($res['count'] >0)
				{
					$id = $res['data'][0]['id'];
					$user_type = $res['data'][0]['user_type'];
					$email = $res['data'][0]['email'];
					$mobile = $res['data'][0]['mobile'];
					$name = $res['data'][0]['name'];
				    $np = rand(100000,999999);
					//$np =rnd_str(6);
					$up = array('password'=>md5($np));
					$ev =$inst_url."/email/".$user_type;
					//$res = update_data($user_type,$up,$id,'id');
					$sms ="Dear ". $name." Your verification link is ".$np ." kindly change after login " .$inst_name ; 
					rtfmail($email,'Email Verification Link' ,$sms ,$noreply_email);
					//bulk_sms($mobile,$sms);
					$data['id'] =$id;
					$data['status'] ='success';
					$data['msg'] ="Verification Link Successfully Send to $email";
					
				}
				else{
					$data['id'] =0;
					$data['status'] ='error';
					$data['msg'] ='No any user exist with this ID. Try Again';
				}
				echo json_encode($data);
				break;
				
			//=============Paper Module ========//
			case "add_paper" :
				extract($_POST);
				$res = insert_data('paper_list',$_POST);
				$res['url'] ='add_paper';
				echo json_encode($res);
				break;
			case "add_docs" :
				extract($_POST);
				$res = insert_html('docs',$_POST);
				$res['url'] ='docs_upload';
				echo json_encode($res);
				break;
			case "add_question" :
				extract($_POST);
				$res = insert_html('qbank',$_POST);
				$res['url'] ='question';
				echo json_encode($res);
				break;
			case "insert_data" :
				$id = $_POST['course_id'];
				$res['course_name'] = get_data('course_details',$id,'course_name' ,'course_id');
				$res['course_duration'] = get_data('course_details',$id,'course_duration' ,'course_id');
				echo json_encode($res);
				break;
			case "delete_course" :
				$res = delete_data('course_details',$_POST['id'],'course_id');
				echo json_encode($res);
				break;
			/*============Website Module ============*/	
			case "add_notice" :
				$res = insert_html('notice', $_POST);
				$res['url'] ='notice';
				echo json_encode($res);
				break;
			case "add_to_gallery" :
			    $_POST['image_date'] =date('Y-m-d');
				$res = insert_data('gallery', $_POST);
			    $res['url'] ='add_to_gallery';
				echo json_encode($res);
				break;
			/*============= MyAdv ===============*/ 
			case  "get_dist":
				$state_code = $_GET['state_code'];
				$res = get_all('district', '*', array('state_code'=>$state_code) , 'name');
				foreach($res['data'] as $data)
				{
					$id = $data['id'];
					//echo "<option value='".$data['district_code']."'>". $data['district_name'] ."</option>" ;
					$list['id']=$data['code'];
					$list['text'] =$data['name'];
					$lf[] = $list;
				}
				echo json_encode($lf);
				break;
				
			case  "get_dist2":
				$state_code = $_GET['state_code'];
				$res = get_all('district', '*', array('state_code'=>$state_code) , 'name');
				foreach($res['data'] as $data)
				{
					
					echo "<option value='".$data['code']."'>". $data['name'] ."</option>" ;
					
				}
				break;
			
			case  "get_univ":
				$state_code = $_GET['state_code'];
				univlist($state_code);
				break;
				
			case  "get_college":
				$univ_code = $_GET['univ_code'];
				$res = get_all('college', '*', array('univ_code'=>$univ_code) , 'name desc');
				foreach($res['data'] as $data)
				{
					echo "<option value='".$data['code']."'>". $data['name'] ."</option>" ;
					
				}
				break;
			case  "bc_list":
					echo dropdownlist('bc','id','name',null);
				break;
				
			case "advocate_profile" :
				//print_r($_POST);
				//$_POST['education'] = implode(',',$_POST['education']);
				$_POST['updated_at'] =date('Y-m-d H:i:s');
				$_POST['practice_area'] = implode(',',$_POST['practice_area']);
				$res = update_data('advocate',$_POST,$_POST['id']);
				$res['url'] ='advocate_dashboard';
				echo json_encode($res);
				break;
				
			case "member_profile" :
				$_POST['updated_at'] =date('Y-m-d H:i:s');
				$res = update_data('member',$_POST,$_POST['id']);
				$res['url'] ='member_dashboard';
				echo json_encode($res);
				break;
			case "add_enquiry" :
				$res = insert_data('contact',$_POST);
				$res['msg'] ='Request submitted successfully. We will contact you soon' ;
				echo json_encode($res);
				break;
			Case "upload" :
				$result =uploadimg('uploadimg', 'rand','upload');
				echo json_encode($result);
				break;	
			
			Case "add_docs" :
				extract($_POST);
				$res = insert_html('docs',$_POST);
				$res['url'] ='docs_upload';
				echo json_encode($res);
				break;
			// Online Law 	
			case  "search_pincode":
				$pincode = $_GET['pincode'];
				$res = get_all('pincode', '*', array('pincode'=>$pincode) , 'name desc');
				foreach($res['data'] as $data)
				{
					echo "<option value='".$data['office_name']."'>". $data['locality_name'] ."</option>" ;
					
				}
				break;
				
			default :
				echo "<script> alert('Invalid Action'); window.location ='".$_SERVER['HTTP_REFERER']."' </script>";	
				
				
		}

}
?>
