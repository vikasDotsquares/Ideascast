<?php

/**
 * Component for working common.

 */
class CommonEmailComponent extends Component {

    public $components = array('Session', 'Email');

    ################  Email send code ####################

    public function resetPassEmail($to, $sub, $userArr) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('admin_password_reset', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr));
        $email->from(array(ADMIN_FROM_EMAIL => 'My dating Site'));
        $email->to($to);
        $email->subject($sub);
        $this->__sendEmail($email);
    }

    public function forgotPassEmail($to, $sub, $userArr) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('admin_forgot_password', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr));
        $email->from(array(ADMIN_FROM_EMAIL => 'My dating Site'));
        $email->to($to);
        $email->subject($sub);
        $this->__sendEmail($email);
    }

    public function activationLinkEmail($to, $sub, $userArr) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('account_activation_url', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr));
        $email->from(array(ADMIN_FROM_EMAIL => 'My dating Site'));
        $email->to($to);
        $email->subject($sub);
        $this->__sendEmail($email);
    }

    public function sendEmailToDoUser($userArr, $todoname, $sender , $type,$pageAction=null,$projectName = null) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('todo_request', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr, "todoname" => $todoname, "senders" => $sender ,"type" => $type,'open_page'=>$pageAction,'projectName'=>$projectName));
        //$email->from(array(ADMIN_EMAIL => 'The IdeasCast Team'));
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($userArr['User']['email']);

		if( $type == 'To-do' ){
			$emailSubject = 'To-do';
		} else {
			$emailSubject = 'Sub To-do';
		}

        $email->subject(SITENAME . ": ".$emailSubject." request");
        $this->__sendEmail($email);
    }

    public function sendEmailWiki($userArr, $wikidata, $type) {

		$project_id = $wikidata['Wiki']['project_id'];
		$projectDetail = getByDbId('Project', $project_id, 'title');
		$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
		$requestAction = SITEURL.'wikies/index/project_id:'.$project_id;

        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('wiki', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr, "wikidata" => $wikidata, "type" => $type,'projectName' => $projectName,'open_page' => $requestAction));
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($userArr['User']['email']);
        $email->subject(SITENAME . ": Wiki " . $type);

        $this->__sendEmail($email);
    }

	public function sendEmailWikiPage($userArr, $wikipagedata, $type) {
		//pr($wikipagedata); die;
		$project_id = $wikipagedata['WikiPage']['project_id'];
		$projectDetail = getByDbId('Project', $project_id, 'title');
		$projectName = ( isset($projectDetail) && !empty($projectDetail['Project']['title']) )? $projectDetail['Project']['title'] : '';
		$requestAction = SITEURL.'wikies/index/project_id:'.$project_id;
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('wiki_page', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr, "wikipagedata" => $wikipagedata, "type" => $type,'projectName' => $projectName,'open_page' => $requestAction));
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME ));
        $email->to($userArr['User']['email']);
        $email->subject(SITENAME . ": Wiki " . $type);
        $this->__sendEmail($email);
    }

    public function sendEmailEditSketchRequest($userArr, $sketch_data) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('sketch', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr, "sketch_data" => $sketch_data));
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($userArr['User']['email']);
        //$email->subject(SITENAME . ": Sketch sharing request");
        $email->subject(SITENAME . ": Sketch edit request");
        $this->__sendEmail($email);
    }

    public function sendEmailDeleteSketch($userArr, $sketch_data) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('sketchdelete', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr, "sketch_data" => $sketch_data));
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($userArr['User']['email']);
        $email->subject(SITENAME . ": Sketch deleted");
        $this->__sendEmail($email);
    }

    public function sendEmailAddSketchParticipant($userArr, $sketch_data) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('sketchparticipant', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr, "sketch_data" => $sketch_data));
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($userArr['User']['email']);
        $email->subject(SITENAME . ": Sketch sharing request");
        $this->__sendEmail($email);
    }

	public function sendEmailRemoveSketchParticipant($userArr, $sketch_data) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('sketchremoveparticipant', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr, "sketch_data" => $sketch_data));
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($userArr['User']['email']);
        $email->subject(SITENAME . ": Sketch sharing removed");
        $this->__sendEmail($email);
    }



    public function AgencyRegistration($ArrDetails = array()) {
        $to = ADMIN_EMAIL;
        $sub = 'Agency Registration';
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('agency_registration', null);
        $email->emailFormat('html');
        $email->viewVars(array('ArrDetails' => $ArrDetails));
        $email->from(array('noreply@inlovebride.com' => 'InLoveBride'));
        $email->to($to);
        $email->subject($sub);
        $this->__sendEmail($email);
    }

    // Mail Send to Admin when agency register any girl profile
    public function AdminAgencyGirlProfileAdded($to, $sub, $userArr) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('admin_girlprofile_added', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr));
        $email->from(array(ADMIN_FROM_EMAIL => 'InLoveBride'));
        $email->to($to);
        $email->subject($sub);
        $this->__sendEmail($email);
    }

    // Mail Send to Girl when agency register
    public function GirlProfileMail($to, $sub, $userArr) {
        $email = new CakeEmail();
		$email->config('Smtp');
        $email->template('girlprofile', null);
        $email->emailFormat('html');
        $email->viewVars(array('users' => $userArr));
        $email->from(array(ADMIN_FROM_EMAIL => 'InLoveBride'));
        $email->to($to);
        $email->subject($sub);
        $this->__sendEmail($email);
    }

    public function __sendEmail($email) {
        if ($email->send())
            return true;
        else
            return false;
    }

    public function user_activation($data) {

        //Send Email and redirect to list page
        $usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $data['user_id'])));

        if(isset($data['password']) && !empty($data['password'])) {
            $userPass = $data['password'];
        }
        else{
            $userPass = $usersDetails['UserDetail']['org_password'];
			$sqlN = "select AES_DECRYPT(org_password, 'secret') as org_password from user_details WHERE user_details.user_id =".$data['user_id'];

		    $dd = ClassRegistry::init('User')->query($sqlN);

			$userPass = $dd[0][0]['org_password'];

        }

        $domain_url = SITEURL;

        $endc = safeEncrypt($usersDetails['User']['email']);
        $activation_url = 'users/activate_account/'.$endc;

        $emailAddress = $usersDetails['User']['email'];
        $email = new CakeEmail();
        $email->config('Smtp');
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($emailAddress);
        $email->subject("OpusView: Account activation");
        $email->template('user_activation');
        $email->emailFormat('html');
        $email->viewVars(array('receiver' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'username' => $usersDetails['User']['email'], 'password' => $userPass, 'domain_url' => $domain_url, 'activation_url' => $activation_url));
        $email->send();
        return true;
    }

    public function user_confirmation($data) {
        //$this->CommonEmail->user_confirmation(['user_id' => $userId]);

        $usersDetails = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $data['user_id'])));

        if(isset($data['password']) && !empty($data['password'])) {
            $userPass = $data['password'];
        }
        else{
            $sqlN = "select AES_DECRYPT(org_password, 'secret') as org_password from user_details WHERE user_details.user_id =".$data['user_id'];

            $dd = ClassRegistry::init('User')->query($sqlN);

            $userPass = $dd[0][0]['org_password'];
        }

        $domain_url = SITEURL;

        $endc = safeEncrypt($usersDetails['User']['email']);
        $confirmation_url = '';

        $emailAddress = $usersDetails['User']['email'];
        $email = new CakeEmail();
        $email->config('Smtp');
        $email->from(array(ADMIN_FROM_EMAIL => MAIL_SITENAME));
        $email->to($emailAddress);
        $email->subject("OpusView: Confirmation of activation");
        $email->template('user_confirmation');
        $email->emailFormat('html');
        $email->viewVars(array('receiver' => $usersDetails['UserDetail']['first_name'] . ' ' . $usersDetails['UserDetail']['last_name'], 'username' => $usersDetails['User']['email'], 'domain_url' => $domain_url, 'confirmation_url' => $confirmation_url));
        $email->send();
        return true;
    }

    ################  Email send code ####################
}
