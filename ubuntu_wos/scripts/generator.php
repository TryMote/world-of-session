<?php 
	function generate() {
		return substr(sha1(mt_rand()),0,22);
	}
?>
