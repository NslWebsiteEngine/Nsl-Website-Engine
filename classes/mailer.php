<?php
class mailer extends base {
	public $from;
	public $from_just_mail;
	public $to;
	public $replay_to;
	public $subject;
	public $additional_headers;
	public $html;

	function from($name, $email = false) {
		if($email) {
			$this->from = "{$name} <{$email}>";
			$this->from_just_mail = $email;
		}
		else
			$this->from = $this->from_just_mail = $name;
		return $this;
	}
	function to($name, $email = false) {
		if($email)
			$this->to = "{$name} <{$email}>";
		else
			$this->to = $name;
		return $this;
	}
	function replay($name, $email = false) {
		if($email)
			$this->replay_to = "{$name} <{$email}>";
		else
			$this->replay_to = $name;
		return $this;
	}
	function subject($subject) {
		$this->subject = $subject;
		return $this;
	}
	function header($name, $value) {
		$this->additional_headers[] = $name.": ".$value;
		return $this;
	}
	function html($html) {
		$this->html = ($html == true || $html == false) ? $html : true;
		return $this;
	}
	function message($message) {
		$this->message = $message;
		return $this;
	}

	function send() {
		if($this->replay)
			$this->headers("Replay-To", $this->replay);
		if($this->html) {
			$this->header("MIME-Version", "1.0");
			$this->header("Content-Type", 'text/html; charset="UTF-8"');
		}
		$message_id = "<".sha1(time()).sha1(microtime(true))."@".$_SERVER["SERVER_NAME"].">";
		$subject = "=?UTF-8?B?".base64_encode($this->subject)."?=";
		$this->header("X-Sender", $this->from);
		$this->header("Subject", $subject);
		$this->header("Content-Transfer-Encoding", "7bit");
		$this->header("Date", date('r', time()));
		$this->header("Message-ID", $message_id);
		$this->header("X-Mailer", "NSL-Website-Engine/".$this->getVersion());
		$this->additional_headers = implode("\r\n", $this->additional_headers);
		return mail($this->to, $subject, $this->message, $this->additional_headers, "-f ".$this->from_just_mail);
	}
}