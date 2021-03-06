<?php
class filesystem extends base {
	private $path = '/';
	private $chroot = "";

	function exists($file) {
		return file_exists($this->path.$file);
	}
	function validdir($path) {
		return substr(realpath($path), 0, strlen($this->chroot))."/" == $this->chroot."/";
	}
	function cd($path) {
		if($this->validdir($path))
			$this->path = $path;
		return $this;
	}
	function write($file, $data) {
		file_put_contents($this->path.$file, $data);
		return $this;
	}
	function read($file) {
		return $this->exists($file) ? file_get_contents($this->path.$file) : "";
	}
	function clear() {
		clearstatcache();
		return $this;
	}
	function delete($file) {
		if($this->exists($file))
			unlink($this->path.$file);
		return $this;
	}
	function own($owner, $file) {
		if($this->exists($file))
			chown($this->path.$file, $owner);
		$this->clear();
		return $this;
	}
	function ownwww($file) { // WARNING: WORKS ONLY ON LINUX
		if(function_exists("shell_exec"))
			return $this->own(trim(shell_exec("whoami")), $file);
		return $this->own(posix_getpwuid(posix_geteuid()), $file);
	}
	function copy($file, $dest, $samepath = true) {
		if($this->exists($file))
			copy($this->path.$file, ($samepath ? $this->path : "").$dest);
		return $this;
	}
	function mkdir($dir) {
		mkdir($this->path.$dir);
		return $this;
	}
	function chroot($chroot) {
		if(substr($chroot, -1) == "/")
			$chroot = substr($chroot, 0, -1);
		$this->chroot = $chroot;
		$this->clear();
		return $this;
	}
	function rename($oldname, $newname, $swap = false) {
		if($this->exists($oldname)) {
			if($swap && $this->exists($newname)) {
				$tmp = $newname.uniqid();
				rename($this->path.$newname, $this->path.$tmp);
				rename($this->path.$oldname, $this->path.$newname);
				rename($this->path.$tmp, $this->path.$oldname);
			}else
				rename($this->path.$oldname, $this->path.$newname);
		}
		return $this;
	}
	function move($oldname, $newname) {
		return rename($oldname, $newname);
	}
}
