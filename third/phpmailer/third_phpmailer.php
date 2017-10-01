<?php
require_once __DILECTIO_THIRD."phpmailer/phpmailer/class.phpmailer.php";
require_once __DILECTIO_THIRD."phpmailer/phpmailer/class.smtp.php";

class third_phpmailer {
	private $mailer = null;
	private $address = null;
	private $username = null;
	
	public function __construct($username, $address) {
		$this->mailer = new PHPMailer(true);
		$this->username = $username;
		$this->address = $address;
	}
	
	public function attach($file) {
		$ret = false;
		if (@file_exists($file)) {
			$this->mailer->AddAttachment($file);
			$ret = true;
		}
		return $ret;
	}
	
	public function send($title, $message) {
		$this->mailer->SMTPDebug = 0;
		$this->mailer->isSMTP();
		$this->mailer->Host = "ssl0.ovh.net";
		$this->mailer->SMTPAuth = true;
		$this->mailer->Username = "no-reply@dilect.io";
		$this->mailer->Password = "L<3veG33k70";
		$this->mailer->SMTPSecure = 'ssl';
		$this->mailer->Port = 465;

		$this->mailer->setFrom("no-reply@dilect.io", "No Reply DILECTIO");
		$this->mailer->addAddress($this->address, $this->username);
		$this->mailer->addReplyTo("no-reply@dilect.io", "No Reply DILECTIO");

		$this->mailer->isHTML(true);
		$this->mailer->CharSet = "UTF-8";
		$this->mailer->Subject = $title;
		$this->mailer->Body = $message;

		$ret = false;
		try {
			$ret = $this->mailer->send();
		} 
		catch (phpmailerException $e) {
			$ret = false;
		}
		catch (Exception $e) {
			$ret = false;
		}
		return $ret;
	}
}