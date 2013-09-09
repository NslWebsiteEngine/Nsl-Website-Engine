<?php
class mailer extends base {
	public $from;
	public $from_just_mail;
	public $to;
	public $reply_to;
	public $subject;
	public $additional_headers;
	public $html;

	//enable smtp
	function smtp() {
		// add swiftmailer to composer requirements
		$this->__composer_requirements = array("swiftmailer/swiftmailer" => "*");
		// set smtp to enabled
		$this->smtp = true;
		// set the smtp mailer
		$this->smtpmailer = true;
		return $this;
	}
	function server($server, $port, $ssl = "") {
		// if smtp is enabled
		if($this->smtp)
			// create a transport for smtp
			$this->transport = Swift_SmtpTransport::newInstance($server, $port, $ssl);
		return $this;
	} 
	function username($username) {
		// if smtp is enabled
		if($this->smtp)
			// set the username
			$this->transport->setUsername($username);
		return $this;
	}
	function password($password) {
		// if smtp is enabled
		if($this->smtp)
			// set the password
			$this->transport->setPassword($password);
		return $this;
	}
	function mailer($subject = "") {
		// if smtp is enabled
		if($this->smtp) {
			// setup the smtpmailer
			$this->smtpmailer = Swift_Mailer::newInstance($this->transport);
			// ... and the message itself
			$this->message = Swift_Message::newInstance($subject);
		}
		return $this;
	}
	function from($name, $email = false) {
		if($this->smtp) {
			if($email)
				$this->message->setFrom(array($email => $name));
			else
				$this->message->setFrom(array($name));
		}else{
			if($email) {
				$this->from = "{$name} <{$email}>";
				$this->from_just_mail = $email;
			}
			else
				$this->from = $this->from_just_mail = $name;	
		}
		return $this;
	}
	function to($name, $email = false) {
		if($this->smtp) {
			if($email)
				$this->message->setTo(array($email => $name));
			else
				$this->message->setTo(array($name));
		}else{
			if($email)
				$this->to = "{$name} <{$email}>";
			else
				$this->to = $name;
		}
		return $this;
	}
	function reply($name, $email = false) {
		if($this->smtp) {
			if($email)
				$this->message->setReplyTo(array($email => $name));
			else
				$this->message->setReplyTo(array($name));
		}else{
			if($email)
				$this->reply_to = "{$name} <{$email}>";
			else
				$this->reply_to = $name;
		}
		return $this;
	}
	function subject($subject) {
		if($this->smtp)
			$this->message->setSubject($subject);
		else
			$this->subject = $subject;
		return $this;
	}
	function header($name, $value) {
		if($this->smtp)
			$this->message->getHeaders()->addTextHeader($name, $value);
		else
			$this->additional_headers[] = $name.": ".$value;
		return $this;
	}
	function html($html = true) {
		$this->html = ($html == true || $html == false) ? $html : true;
		return $this;
	}
	function message($message) {
		$this->email_content = $message;
		return $this;
	}

	function send() {
		if($this->smtp) {
			if($this->html) {
				// set the message to html as standard
				$this->message->setBody($this->email_content, "text/html");
			}else
				$this->message->setBody($this->email_content, "text/plain");
			// RFC 2822 -> Section 2.3 Body -> a line can be at most 998 characters long + the CRLF (\r\n)
			# http://www.rfc-editor.org/pdfrfc/rfc2822.txt.pdf
			$this->message->setMaxLineLength(1000);
			return $this->smtpmailer->send($this->message);
		}else{
			if($this->replay)
				$this->headers("Reply-To", $this->reply_to);
			if($this->html) {
				$this->header("MIME-Version", "1.0");
				$this->header("Content-Type", 'text/html; charset="UTF-8"');
			}
			if(function_exists("uniqid"))
				$message_id = "<".sha1("NSL-Website-Engine").sha1(uniqid("NSL-Website-Engine"))."@".$_SERVER["SERVER_NAME"].">";
			else
				$message_id = "<".sha1(time()).sha1(microtime(true))."@".$_SERVER["SERVER_NAME"].">";
			$subject = "=?UTF-8?B?".base64_encode($this->subject)."?=";
			$this->header("X-Sender", $this->from);
			$this->header("Subject", $subject);
			$this->header("Content-Transfer-Encoding", "7bit");
			$this->header("Date", date('r', time()));
			$this->header("Message-ID", $message_id);
			$this->header("X-Mailer", "NSL-Website-Engine/".$this->getVersion());
			$this->additional_headers = implode("\r\n", $this->additional_headers);
			return mail($this->to, $subject, $this->email_content, $this->additional_headers, "-f ".$this->from_just_mail);
		}
	}
}