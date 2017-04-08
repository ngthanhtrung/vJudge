<?php
	defined('_SECURITY') or die("Access denied.");
?>
function login() {
	post('com=user&q=do:login', createParam('user-log-form'));
}
function submitUserAdd() {
	post('com=user&q=do:submitregister', createParam('user-register-form'));
}