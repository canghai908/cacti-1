<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2004-2015 The Cacti Group                                 |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 2          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
 | Cacti: The Complete RRDTool-based Graphing Solution                     |
 +-------------------------------------------------------------------------+
 | This code is designed, written, and maintained by the Cacti Group. See  |
 | about.php and/or the AUTHORS file for specific developer information.   |
 +-------------------------------------------------------------------------+
 | http://www.cacti.net/                                                   |
 +-------------------------------------------------------------------------+
*/

include('./include/global.php');

/* check to see if this is a new installation */
$version = db_fetch_cell('SELECT cacti FROM version');
if ($version != $config['cacti_version']) {
	header ('Location: ' . $config['url_path'] . 'install/');
	exit;
}

if (basename($_SERVER['PHP_SELF']) == 'logout.php') {
	return true;
}

if (read_config_option('auth_method') != 0) {
	/* handle alternate authentication realms */
	api_plugin_hook_function('auth_alternate_realms');

	/* handle change password dialog */
	if ((isset($_SESSION['sess_change_password'])) && (read_config_option('webbasic_enabled') != 'on')) {
		header ('Location: ' . $config['url_path'] . 'auth_changepassword.php?ref=' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php'));
		exit;
	}

	/* check for remember me function ality */
	if (!isset($_SESSION['sess_user_id'])) {
		$cookie_user = check_auth_cookie();
		if ($cookie_user !== false) {
			$_SESSION['sess_user_id'] = $cookie_user;
		}
	}

	/* don't even bother with the guest code if we're already logged in */
	if ((isset($guest_account)) && (empty($_SESSION['sess_user_id']))) {
		$guest_user_id = db_fetch_cell_prepared('SELECT id FROM user_auth WHERE username = ? AND realm = 0 AND enabled = "on"', array(read_config_option('guest_user')));

		/* cannot find guest user */
		if (!empty($guest_user_id)) {
			$_SESSION['sess_user_id'] = $guest_user_id;
			return true;
		}
	}

	/* if we are a guest user in a non-guest area, wipe credentials */
	if (!empty($_SESSION['sess_user_id'])) {
		if ((!isset($guest_account)) && (db_fetch_cell_prepared('SELECT id FROM user_auth WHERE username = ?', array(read_config_option('guest_user'))) == $_SESSION['sess_user_id'])) {
			kill_session_var('sess_user_id');
		}
	}

	if (empty($_SESSION['sess_user_id'])) {
		include('./auth_login.php');
		exit;
	}elseif (!empty($_SESSION['sess_user_id'])) {
		$realm_id = 0;

		if (isset($user_auth_realm_filenames{basename($_SERVER['PHP_SELF'])})) {
			$realm_id = $user_auth_realm_filenames{basename($_SERVER['PHP_SELF'])};
		}

		if ($realm_id > 0) {
			$authorized = db_fetch_cell_prepared('SELECT COUNT(*) 
				FROM (
					SELECT realm_id
					FROM user_auth_realm AS uar
					WHERE uar.user_id = ?
					AND uar.realm_id = ?
					UNION
					SELECT realm_id
					FROM user_auth_group_realm AS uagr
					INNER JOIN user_auth_group_members AS uagm
					ON uagr.group_id=uagm.group_id
					INNER JOIN user_auth_group AS uag
					ON uag.id=uagr.group_id
					WHERE uag.enabled="on"
					AND uagm.user_id = ?
					AND uagr.realm_id = ?
				) AS authorized', array($_SESSION['sess_user_id'], $realm_id, $_SESSION['sess_user_id'], $realm_id));
		}else{
			$authorized = false;
		}


		if ($realm_id != -1 && !$authorized) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$goBack = "<td colspan='2' align='center'>[<a href='" . htmlspecialchars($_SERVER['HTTP_REFERER']) . "'>Return</a> | <a href='" . $config['url_path'] . "logout.php'>Login Again</a>]</td>";
			}else{
				$goBack = "<td colspan='2' align='center'>[<a href='" . $config['url_path'] . "logout.php'>Login Again</a>]</td>";
			}

			print "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>\n";
			print "<html>\n";
			print "<head>\n";
			print "\t<title>Permission Denied</title>\n";
			print "\t<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>\n";
			print "\t<link href='" . $config['url_path'] . "include/themes/" . read_config_option('selected_theme') . "/main.css' type='text/css' rel='stylesheet'>\n";
			print "\t<link href='" . $config['url_path'] . "include/themes/" . read_config_option('selected_theme') . "/jquery-ui.css' type='text/css' rel='stylesheet'>\n";
			print "\t<link href='" . $config['url_path'] . "images/favicon.ico' rel='shortcut icon'>\n";
			print "\t<script type='text/javascript' src='" . $config['url_path'] . "include/js/jquery.js' language='javascript'></script>\n";
			print "\t<script type='text/javascript' src='" . $config['url_path'] . "include/js/jquery-ui.js' language='javascript'></script>\n";
			print "\t<script type='text/javascript' src='" . $config['url_path'] . "include/js/jquery.cookie.js' language='javascript'></script>\n";
			print "\t<script type='text/javascript' src='" . $config['url_path'] . "include/js/jquery.hotkeys.js'></script>\n";
			print "\t<script type='text/javascript' src='" . $config['url_path'] . "include/layout.js'></script>\n";
			print "\t<script type='text/javascript' src='" . $config['url_path'] . "include/themes/". read_config_option('selected_theme') . "/main.js'></script>\n";
			print "<script type='text/javascript'>var theme='" . read_config_option('selected_theme') . "';</script>\n";
			print "</head>\n";
			print "<body class='logoutBody'>
			<div class='logoutLeft'></div>
			<div class='logoutCenter'>
				<div class='logoutArea'>
					<div class='cactiLogoutLogo'></div>
					<legend>Permission Denied</legend>
					<div class='logoutTitle'>
						<p>You are not permitted to access this section of Cacti.<br>
						If you feel that this is an error.  Please contact your<br>
						Cacti Administrator.</p>
						<center>" . $goBack . "</center>
					</div>
					<div class='logoutErrors'></div>
				</div>
				<div class='versionInfo'>Version " . $version . " | " . COPYRIGHT_YEARS_SHORT . "</div>
			</div>
			<div class='logoutRight'></div>
			<script type='text/javascript'>
			$(function() {
				$('.loginLeft').css('width',parseInt($(window).width()*0.33)+'px');
				$('.loginRight').css('width',parseInt($(window).width()*0.33)+'px');
			});
			</script>
			</body>
			</html>\n";
			exit;
		}
	}
}

