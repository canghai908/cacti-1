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

function upgrade_to_0_8_8d() {
	global $database_default;

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `user_auth_group` (
		`id` int(10) unsigned NOT NULL auto_increment,
		`name` varchar(20) NOT NULL,
		`description` varchar(255) NOT NULL default '',
		`graph_settings` varchar(2) DEFAULT NULL,
		`login_opts` tinyint(1) NOT NULL DEFAULT '1',
		`show_tree` varchar(2) DEFAULT 'on',
		`show_list` varchar(2) DEFAULT 'on',
		`show_preview` varchar(2) NOT NULL DEFAULT 'on',
		`policy_graphs` tinyint(1) unsigned NOT NULL DEFAULT '1',
		`policy_trees` tinyint(1) unsigned NOT NULL DEFAULT '1',
		`policy_hosts` tinyint(1) unsigned NOT NULL DEFAULT '1',
		`policy_graph_templates` tinyint(1) unsigned NOT NULL DEFAULT '1',
		`enabled` char(2) NOT NULL DEFAULT 'on',
		PRIMARY KEY (`id`))
		ENGINE=MyISAM
		COMMENT='Table that Contains User Groups';");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `user_auth_group_perms` (
		`group_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
		`item_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
		`type` tinyint(2) unsigned NOT NULL DEFAULT '0',
		PRIMARY KEY (`group_id`,`item_id`,`type`),
		KEY `group_id` (`group_id`,`type`))
		ENGINE=MyISAM
		COMMENT='Table that Contains User Group Permissions';");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `user_auth_group_realm` (
		`group_id` int(10) unsigned NOT NULL,
		`realm_id` int(10) unsigned NOT NULL,
		PRIMARY KEY  (`group_id`, `realm_id`),
		KEY `group_id` (`group_id`),
		KEY `realm_id` (`realm_id`))
		ENGINE=MyISAM
		COMMENT='Table that Contains User Group Realm Permissions';");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `user_auth_group_members` (
		`group_id` int(10) unsigned NOT NULL,
		`user_id` int(10) unsigned NOT NULL,
		PRIMARY KEY  (`group_id`, `user_id`),
		KEY `group_id` (`group_id`),
		KEY `realm_id` (`user_id`))
		ENGINE=MyISAM
		COMMENT='Table that Contains User Group Members';");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `settings_graphs_group` (
		`group_id` smallint(8) unsigned NOT NULL DEFAULT '0',
		`name` varchar(50) NOT NULL DEFAULT '',
		`value` varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY (`group_id`,`name`))
		ENGINE=MyISAM
		COMMENT='Stores the Default User Group Graph Settings';");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_stats_daily` (
		`local_data_id` mediumint(8) unsigned NOT NULL,
		`rrd_name` varchar(19) NOT NULL,
		`average` DOUBLE DEFAULT NULL,
		`peak` DOUBLE DEFAULT NULL,
		PRIMARY KEY  (`local_data_id`,`rrd_name`)
		) ENGINE=MyISAM;");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_stats_hourly` (
		`local_data_id` mediumint(8) unsigned NOT NULL,
		`rrd_name` varchar(19) NOT NULL,
		`average` DOUBLE DEFAULT NULL,
		`peak` DOUBLE DEFAULT NULL,
		PRIMARY KEY  (`local_data_id`,`rrd_name`)
		) ENGINE=MyISAM;");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_stats_hourly_cache` (
		`local_data_id` mediumint(8) unsigned NOT NULL,
		`rrd_name` varchar(19) NOT NULL,
		`time` timestamp NOT NULL default '0000-00-00 00:00:00',
		`value` DOUBLE DEFAULT NULL,
		PRIMARY KEY  (`local_data_id`,`time`,`rrd_name`),
		KEY `time` USING BTREE (`time`)
		) ENGINE=MEMORY;");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_stats_hourly_last` (
		`local_data_id` mediumint(8) unsigned NOT NULL,
		`rrd_name` varchar(19) NOT NULL,
		`value` DOUBLE DEFAULT NULL,
		`calculated` DOUBLE DEFAULT NULL,
		PRIMARY KEY  (`local_data_id`,`rrd_name`)
		) ENGINE=MEMORY;");

	if (!sizeof(db_fetch_row("SHOW COLUMNS from data_source_stats_hourly_last where Field='calculated'"))) {
		db_install_execute('0.8.8d', "ALTER TABLE data_source_stats_hourly_last ADD calculated DOUBLE DEFAULT NULL AFTER `value`");
	};

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_stats_monthly` (
		`local_data_id` mediumint(8) unsigned NOT NULL,
		`rrd_name` varchar(19) NOT NULL,
		`average` DOUBLE DEFAULT NULL,
		`peak` DOUBLE DEFAULT NULL,
		PRIMARY KEY  (`local_data_id`,`rrd_name`)
		) ENGINE=MyISAM;"
	);

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_stats_weekly` (
		`local_data_id` mediumint(8) unsigned NOT NULL,
		`rrd_name` varchar(19) NOT NULL,
		`average` DOUBLE DEFAULT NULL,
		`peak` DOUBLE DEFAULT NULL,
		PRIMARY KEY  (`local_data_id`,`rrd_name`)
		) ENGINE=MyISAM;"
	);

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_stats_yearly` (
		`local_data_id` mediumint(8) unsigned NOT NULL,
		`rrd_name` varchar(19) NOT NULL,
		`average` DOUBLE DEFAULT NULL,
		`peak` DOUBLE DEFAULT NULL,
		PRIMARY KEY  (`local_data_id`,`rrd_name`)
		) ENGINE=MyISAM;"
	);

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `poller_output_boost` (
		`local_data_id` mediumint(8) unsigned NOT NULL default '0',
		`rrd_name` varchar(19) NOT NULL default '',
		`time` datetime NOT NULL default '0000-00-00 00:00:00',
		`output` varchar(512) NOT NULL,
		PRIMARY KEY USING BTREE (`local_data_id`,`time`,`rrd_name`))
		ENGINE=MEMORY;");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `poller_output_boost_processes` (
		`sock_int_value` bigint(20) unsigned NOT NULL auto_increment,
		`status` varchar(255) default NULL,
		PRIMARY KEY (`sock_int_value`))
		ENGINE=MEMORY;");

	if (db_table_exists('plugin_domains')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_domains TO user_domains');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `user_domains` (
		`domain_id` int(10) unsigned NOT NULL auto_increment,
		`domain_name` varchar(20) NOT NULL,
		`type` int(10) UNSIGNED NOT NULL DEFAULT '0',
		`enabled` char(2) NOT NULL DEFAULT 'on',
		`defdomain` tinyint(3) NOT NULL DEFAULT '0',
		`user_id` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`domain_id`))
		ENGINE=MyISAM
		COMMENT='Table to Hold Login Domains';");

	if (db_table_exists('plugin_domains_ldsp')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_domains_ldsp TO user_domains_ldap');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `user_domains_ldap` (
		`domain_id` int(10) unsigned NOT NULL,
		`server` varchar(128) NOT NULL,
		`port` int(10) unsigned NOT NULL,
		`port_ssl` int(10) unsigned NOT NULL,
		`proto_version` tinyint(3) unsigned NOT NULL,
		`encryption` tinyint(3) unsigned NOT NULL,
		`referrals` tinyint(3) unsigned NOT NULL,
		`mode` tinyint(3) unsigned NOT NULL,
		`dn` varchar(128) NOT NULL,
		`group_require` char(2) NOT NULL,
		`group_dn` varchar(128) NOT NULL,
		`group_attrib` varchar(128) NOT NULL,
		`group_member_type` tinyint(3) unsigned NOT NULL,
		`search_base` varchar(128) NOT NULL,
		`search_filter` varchar(128) NOT NULL,
		`specific_dn` varchar(128) NOT NULL,
		`specific_password` varchar(128) NOT NULL,
		PRIMARY KEY  (`domain_id`))
		ENGINE=MyISAM
		COMMENT='Table to Hold Login Domains for LDAP';");
	if (db_table_exists('plugin_snmpagent_cache')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_snmpagent_cache TO snmpagent_cache');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `snmpagent_cache` (
		`oid` varchar(255) NOT NULL,
		`name` varchar(255) NOT NULL,
		`mib` varchar(255) NOT NULL,
		`type` varchar(255) NOT NULL DEFAULT '',
		`otype` varchar(255) NOT NULL DEFAULT '',
		`kind` varchar(255) NOT NULL DEFAULT '',
		`max-access` varchar(255) NOT NULL DEFAULT 'not-accessible',
		`value` varchar(255) NOT NULL DEFAULT '',
		`description` varchar(5000) NOT NULL DEFAULT '',
		PRIMARY KEY (`oid`),
		KEY `name` (`name`),
		KEY `mib` (`mib`))
		ENGINE=MyISAM
		COMMENT='SNMP MIB CACHE';");

	if (db_table_exists('plugin_snmpagent_mibs')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_snmpagent_mibs TO snmpagent_mibs');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `snmpagent_mibs` (
		`id` int(8) NOT NULL AUTO_INCREMENT,
		`name` varchar(32) NOT NULL DEFAULT '',
		`file` varchar(255) NOT NULL DEFAULT '',
		PRIMARY KEY (`id`))
		ENGINE=MyISAM
		COMMENT='Registered MIB files';");

	if (db_table_exists('plugin_snmpagent_cache_notifications')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_snmpagent_cache_notifications TO snmpagent_cache_notifications');
	}
	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `snmpagent_cache_notifications` (
		`name` varchar(255) NOT NULL,
		`mib` varchar(255) NOT NULL,
		`attribute` varchar(255) NOT NULL,
		`sequence_id` smallint(6) NOT NULL,
		KEY `name` (`name`))
		ENGINE=MyISAM
		COMMENT='Notifcations and related attributes';");

	if (db_table_exists('plugin_snmpagent_cache_textual_conventions')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_snmpagent_cache_textual_conventions TO snmpagent_cache_textual_conventions');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `snmpagent_cache_textual_conventions` (
		`name` varchar(255) NOT NULL,
		`mib` varchar(255) NOT NULL,
		`type` varchar(255) NOT NULL DEFAULT '',
		`description` varchar(5000) NOT NULL DEFAULT '',
		KEY `name` (`name`),
		KEY `mib` (`mib`))
		ENGINE=MyISAM
		COMMENT='Textual conventions';");

	if (db_table_exists('plugin_snmpagent_managers')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_snmpagent_managers TO snmpagent_managers');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `snmpagent_managers` (
		`id` int(8) NOT NULL AUTO_INCREMENT,
		`hostname` varchar(255) NOT NULL,
		`description` varchar(255) NOT NULL,
		`disabled` char(2) DEFAULT NULL,
		`max_log_size` tinyint(1) NOT NULL,
		`snmp_version` varchar(255) NOT NULL,
		`snmp_community` varchar(255) NOT NULL,
		`snmp_username` varchar(255) NOT NULL,
		`snmp_auth_password` varchar(255) NOT NULL,
		`snmp_auth_protocol` varchar(255) NOT NULL,
		`snmp_priv_password` varchar(255) NOT NULL,
		`snmp_priv_protocol` varchar(255) NOT NULL,
		`snmp_port` varchar(255) NOT NULL,
		`snmp_message_type` tinyint(1) NOT NULL,
		`notes` text,
		PRIMARY KEY (`id`),
		KEY `hostname` (`hostname`))
		ENGINE=MyISAM
		COMMENT='snmp notification receivers';");

	if (db_table_exists('plugin_snmpagent_managers_notifications')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_snmpagent_managers_notifications TO snmpagent_managers_notifications');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `snmpagent_managers_notifications` (
		`manager_id` int(8) NOT NULL,
		`notification` varchar(255) NOT NULL,
		`mib` varchar(255) NOT NULL,
		KEY `mib` (`mib`),
		KEY `manager_id` (`manager_id`),
		KEY `manager_id2` (`manager_id`,`notification`))
		ENGINE=MyISAM
		COMMENT='snmp notifications to receivers';");

	if (db_table_exists('plugin_snmpagent_notifications_log')) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_snmpagent_notifications_log TO snmpagent_notifications_log');
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `snmpagent_notifications_log` (
		`id` int(12) NOT NULL AUTO_INCREMENT,
		`time` int(24) NOT NULL,
		`severity` tinyint(1) NOT NULL,
		`manager_id` int(8) NOT NULL,
		`notification` varchar(255) NOT NULL,
		`mib` varchar(255) NOT NULL,
		`varbinds` varchar(5000) NOT NULL,
		PRIMARY KEY (`id`),
		KEY `time` (`time`),
		KEY `severity` (`severity`),
		KEY `manager_id` (`manager_id`),
		KEY `manager_id2` (`manager_id`,`notification`))
		ENGINE=MyISAM
		COMMENT='logs snmp notifications to receivers';");
		
	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_purge_temp` (
		`id` integer UNSIGNED auto_increment,
		`name_cache` varchar(255) NOT NULL default '',
		`local_data_id` mediumint(8) unsigned NOT NULL default '0',
		`name` varchar(128) NOT NULL default '',
		`size` integer UNSIGNED NOT NULL default '0',
		`last_mod` TIMESTAMP NOT NULL default '0000-00-00 00:00:00',
		`in_cacti` tinyint NOT NULL default '0',
		`data_template_id` mediumint(8) unsigned NOT NULL default '0',
		PRIMARY KEY (`id`),
		UNIQUE KEY name (`name`), 
		KEY local_data_id (`local_data_id`), 
		KEY in_cacti (`in_cacti`), 
		KEY data_template_id (`data_template_id`)) 
		ENGINE=MyISAM 
		COMMENT='RRD Cleaner File Repository';");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `data_source_purge_action` (
		`id` integer UNSIGNED auto_increment,
		`name` varchar(128) NOT NULL default '',
		`local_data_id` mediumint(8) unsigned NOT NULL default '0',
		`action` tinyint(2) NOT NULL default 0,
		PRIMARY KEY (`id`),
		UNIQUE KEY name (`name`))
		ENGINE=MyISAM 
		COMMENT='RRD Cleaner File Actions';");

	db_install_execute('0.8.8d', "ALTER TABLE graph_tree 
		ADD COLUMN enabled char(2) DEFAULT 'on' AFTER id,
		ADD COLUMN locked TINYINT default '0' AFTER enabled, 
		ADD COLUMN locked_date TIMESTAMP default '0000-00-00' AFTER locked, 
		ADD COLUMN last_modified TIMESTAMP default '0000-00-00' AFTER name, 
		ADD COLUMN user_id INT UNSIGNED default '1' AFTER name, 
		ADD COLUMN modified_by INT UNSIGNED default '1'");

	db_install_execute('0.8.8d', "ALTER TABLE graph_tree_items 
		MODIFY COLUMN id BIGINT UNSIGNED NOT NULL auto_increment, 
		ADD COLUMN parent BIGINT UNSIGNED default NULL AFTER id, 
		ADD COLUMN position int UNSIGNED default NULL AFTER parent,
		ADD INDEX parent (parent)");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `user_auth_cache` (
		`user_id` int(10) unsigned NOT NULL DEFAULT '0',
		`hostname` varchar(64) NOT NULL DEFAULT '',
		`last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`token` varchar(1024) NOT NULL DEFAULT '') 
		ENGINE=MyISAM 
		COMMENT='Caches Remember Me Details'");

	// Add secpass fields
	db_install_add_column ('0.8.8d', 'user_auth', array('name' => 'lastchange', 'type' => 'int(12)', 'NULL' => false, 'default' => '-1'));
	db_install_add_column ('0.8.8d', 'user_auth', array('name' => 'lastlogin', 'type' => 'int(12)', 'NULL' => false, 'default' => '-1'));
	db_install_add_column ('0.8.8d', 'user_auth', array('name' => 'password_history', 'type' => 'text', 'NULL' => false, 'default' => ''));
	db_install_add_column ('0.8.8d', 'user_auth', array('name' => 'locked', 'type' => 'varchar(3)', 'NULL' => false, 'default' => ''));
	db_install_add_column ('0.8.8d', 'user_auth', array('name' => 'failed_attempts', 'type' => 'int(5)', 'NULL' => false, 'default' => '0'));
	db_install_add_column ('0.8.8d', 'user_auth', array('name' => 'lastfail', 'type' => 'int(12)', 'NULL' => false, 'default' => '0'));

	// Convert all trees to new format, but never run more than once
	$columns = array_rekey(db_fetch_assoc("SHOW COLUMNS FROM graph_tree_items"), "Field", array("Type", "Null", "Key", "Default", "Extra"));

	if (isset($columns['order_key'])) {
		$trees = db_fetch_assoc("SELECT id FROM graph_tree ORDER BY id");

		if (sizeof($trees)) {
		foreach($trees as $t) {
			$tree_items = db_fetch_assoc("SELECT * 
				FROM graph_tree_items 
				WHERE graph_tree_id=" . $t['id'] . " 
				AND order_key NOT LIKE '___000%' 
				ORDER BY order_key");

			/* reset the position variable in case we run more than once */
			db_execute("UPDATE graph_tree_items SET position=0 WHERE graph_tree_id=" . $t['id']);

			$prev_parent = 0;
			$prev_id     = 0;
			$position    = 0;

			if (sizeof($tree_items)) {
				foreach($tree_items AS $item) {
					$translated_key = rtrim($item["order_key"], "0\r\n");
					$missing_len    = strlen($translated_key) % CHARS_PER_TIER;
					if ($missing_len > 0) {
						$translated_key .= substr("000", 0, $missing_len);
					}
					$parent_key_len = strlen($translated_key) - CHARS_PER_TIER;
					$parent_key     = substr($translated_key, 0, $parent_key_len);
					$parent_id      = db_fetch_cell("SELECT id FROM graph_tree_items WHERE graph_tree_id=" . $item["graph_tree_id"] . " AND order_key LIKE '" . $parent_key . "000%'");
	
					if (!empty($parent_id)) {
						/* get order */
						if ($parent_id != $prev_parent) {
							$position = 0;
						}

						$position = db_fetch_cell("SELECT MAX(position) 
							FROM graph_tree_items 
							WHERE graph_tree_id=" . $item['graph_tree_id'] . " 
							AND parent=" . $parent_id) + 1;

						db_execute("UPDATE graph_tree_items SET parent=$parent_id, position=$position WHERE id=" . $item["id"]);
					}else{
						db_execute("UPDATE graph_tree_items SET parent=0, position=$position WHERE id=" . $item["id"]);
					}

					$prev_parent = $parent_id;
				}
			}

			/* get base tree items and set position */
			$tree_items = db_fetch_assoc("SELECT * 
				FROM graph_tree_items
				WHERE graph_tree_id=" . $t['id'] . " 
				AND order_key LIKE '___000%' 
				ORDER BY order_key");

			$position = 0;
			if (sizeof($tree_items)) {
				foreach($tree_items as $item) {
					db_execute("UPDATE graph_tree_items SET parent=0, position=$position WHERE id=" . $item['id']);
					$position++;
				}
			}
		}
		}

		db_install_execute('0.8.8d', "ALTER TABLE graph_tree_items DROP COLUMN order_key");
	}

	/* merge of clog */
	/* clog user = 19 */
	/* dlog admin = 18 */
	$realms = db_fetch_assoc("SELECT * FROM plugin_realms WHERE plugin = 'clog'");
	if (sizeof($realms)) {
	foreach($realms as $r) {
		if ($r['file'] == 'clog.php') {
			db_execute("UPDATE user_auth_realm SET realm_id=18 WHERE realm_id=" . ($r['id']+100));
		}elseif ($r['file'] == 'clog_user.php') {
			db_execute("UPDATE user_auth_realm SET realm_id=19 WHERE realm_id=" . ($r['id']+100));
		}
	}
	}

	db_install_execute('0.8.8d', "DELETE FROM plugin_realms WHERE file LIKE 'clog%'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_config WHERE directory='clog'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_hooks WHERE name='clog'");

	snmpagent_cache_install();

	// Adding email column for future user
	db_install_add_column ('0.8.8d', 'user_auth', array('name' => 'email_address', 'type' => 'varchar(128)', 'NULL' => true, 'after' => 'full_name'));

	db_install_execute('0.8.8d', 'DROP TABLE IF EXISTS poller_output_realtime');
	db_install_execute('0.8.8d', "CREATE TABLE poller_output_realtime (
		local_data_id mediumint(8) unsigned NOT NULL default '0',
		rrd_name varchar(19) NOT NULL default '',
		time timestamp NOT NULL default '0000-00-00 00:00:00',
		output text NOT NULL,
		poller_id varchar(30) NOT NULL default '',
		PRIMARY KEY  (local_data_id,rrd_name,`time`),
		KEY poller_id(poller_id)) 
		ENGINE=MyISAM");

	db_install_execute('0.8.8d', 'DROP TABLE IF EXISTS poller_output_rt');

	db_install_execute('0.8.8d', "DELETE FROM plugin_realms WHERE file LIKE '%graph_image_rt%'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_config WHERE directory='realtime'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_hooks WHERE name='realtime'");

	// If we have never install Nectar before, we can simply install
	if (!sizeof(db_fetch_row("SHOW TABLES LIKE '%plugin_nectar%'"))) {
		db_install_execute('0.8.8d', "CREATE TABLE `reports` (
			`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			`user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
			`name` varchar(100) NOT NULL DEFAULT '',
			`cformat` char(2) NOT NULL DEFAULT '',
			`format_file` varchar(255) NOT NULL DEFAULT '',
			`font_size` smallint(2) unsigned NOT NULL DEFAULT '0',
			`alignment` smallint(2) unsigned NOT NULL DEFAULT '0',
			`graph_linked` char(2) NOT NULL DEFAULT '',
			`intrvl` smallint(2) unsigned NOT NULL DEFAULT '0',
			`count` smallint(2) unsigned NOT NULL DEFAULT '0',
			`offset` int(12) unsigned NOT NULL DEFAULT '0',
			`mailtime` bigint(20) unsigned NOT NULL DEFAULT '0',
			`subject` varchar(64) NOT NULL DEFAULT '',
			`from_name` varchar(40) NOT NULL,
			`from_email` text NOT NULL,
			`email` text NOT NULL,
			`bcc` text NOT NULL,
			`attachment_type` smallint(2) unsigned NOT NULL DEFAULT '1',
			`graph_height` smallint(2) unsigned NOT NULL DEFAULT '0',
			`graph_width` smallint(2) unsigned NOT NULL DEFAULT '0',
			`graph_columns` smallint(2) unsigned NOT NULL DEFAULT '0',
			`thumbnails` char(2) NOT NULL DEFAULT '',
			`lastsent` bigint(20) unsigned NOT NULL DEFAULT '0',
			`enabled` char(2) DEFAULT '',
			PRIMARY KEY (`id`),
			KEY `mailtime` (`mailtime`)) 
			ENGINE=MyISAM 
			COMMENT='Cacri Reporting Reports'");
	
		db_install_execute('0.8.8d', "CREATE TABLE `reports_items` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`report_id` int(10) unsigned NOT NULL DEFAULT '0',
			`item_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
			`tree_id` int(10) unsigned NOT NULL DEFAULT '0',
			`branch_id` int(10) unsigned NOT NULL DEFAULT '0',
			`tree_cascade` char(2) NOT NULL DEFAULT '',
			`graph_name_regexp` varchar(128) NOT NULL DEFAULT '',
			`host_template_id` int(10) unsigned NOT NULL DEFAULT '0',
			`host_id` int(10) unsigned NOT NULL DEFAULT '0',
			`graph_template_id` int(10) unsigned NOT NULL DEFAULT '0',
			`local_graph_id` int(10) unsigned NOT NULL DEFAULT '0',
			`timespan` int(10) unsigned NOT NULL DEFAULT '0',
			`align` tinyint(1) unsigned NOT NULL DEFAULT '1',
			`item_text` text NOT NULL,
			`font_size` smallint(2) unsigned NOT NULL DEFAULT '10',
			`sequence` smallint(5) unsigned NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			KEY `report_id` (`report_id`)) 
			ENGINE=MyISAM 
			COMMENT='Cacti Reporting Items'");
	}else{
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_nectar TO reports');
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_nectar_items TO reports_items');
		db_install_execute('0.8.8d', "UPDATE settings SET name=REPLACE(name, 'nectar','reports') WHERE name LIKE '%nectar%'");

		db_install_add_column ('0.8.8d', 'reports', array('name' => 'bcc',           'type' => 'TEXT', 'after' => 'email'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'from_name',     'type' => 'VARCHAR(40)',  'NULL' => false, 'default' => '', 'after' => 'mailtime'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'user_id',       'type' => 'mediumint(8)', 'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'id'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'graph_width',   'type' => 'smallint(2)',  'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'attachment_type'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'graph_height',  'type' => 'smallint(2)',  'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'graph_width'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'graph_columns', 'type' => 'smallint(2)',  'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'graph_height'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'thumbnails',    'type' => 'char(2)',      'NULL' => false, 'default' => '', 'after' => 'graph_columns'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'font_size',     'type' => 'smallint(2)',  'NULL' => false, 'default' => '16', 'after' => 'name'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'alignment',     'type' => 'smallint(2)',  'NULL' => false, 'default' => '0', 'after' => 'font_size'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'cformat',       'type' => 'char(2)',      'NULL' => false, 'default' => '', 'after' => 'name'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'format_file',   'type' => 'varchar(255)', 'NULL' => false, 'default' => '', 'after' => 'cformat'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'graph_linked',  'type' => 'char(2)',      'NULL' => false, 'default' => '', 'after' => 'alignment'));
		db_install_add_column ('0.8.8d', 'reports', array('name' => 'subject',       'type' => 'varchar(64)',  'NULL' => false, 'default' => '', 'after' => 'mailtime'));

		/* plugin_reports_items upgrade */
		db_install_add_column ('0.8.8d', 'reports_items', array('name' => 'host_template_id',  'type' => 'int(10)', 'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'item_type'));
		db_install_add_column ('0.8.8d', 'reports_items', array('name' => 'graph_template_id', 'type' => 'int(10)', 'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'host_id'));
		db_install_add_column ('0.8.8d', 'reports_items', array('name' => 'tree_id',           'type' => 'int(10)', 'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'item_type'));
		db_install_add_column ('0.8.8d', 'reports_items', array('name' => 'branch_id',         'type' => 'int(10)', 'unsigned' => true, 'NULL' => false, 'default' => '0', 'after' => 'tree_id'));
		db_install_add_column ('0.8.8d', 'reports_items', array('name' => 'tree_cascade',      'type' => 'char(2)', 'NULL' => false, 'default' => '', 'after' => 'branch_id'));
		db_install_add_column ('0.8.8d', 'reports_items', array('name' => 'graph_name_regexp', 'type' => 'varchar(128)', 'NULL' => false, 'default' => '', 'after' => 'tree_cascade'));


		/* fix host templates and graph template ids */
		$items = db_fetch_assoc("SELECT * FROM reports_items WHERE item_type=1");
		if (sizeof($items)) {
		foreach ($items as $row) {
			$host = db_fetch_row("SELECT host.* 
				FROM graph_local 
				LEFT JOIN host 
				ON (graph_local.host_id=host.id) 
				WHERE graph_local.id=" . $row["local_graph_id"]);

			$graph_template = db_fetch_cell("SELECT graph_template_id 
				FROM graph_local 
				WHERE id=" . $row["local_graph_id"]);

			db_execute("UPDATE reports_items SET " .
					" host_id='" . $host["id"] . "', " .
					" host_template_id='" . $host["host_template_id"] . "', " .
					" graph_template_id='" . $graph_template . "' " .
					" WHERE id=" . $row["id"]);
		}
		}
	}

	db_install_add_column ('0.8.8d', 'host', array('name' => 'snmp_sysDescr',          'type' => 'varchar(300)', 'NULL' => false, 'default' => '',  'after' => 'snmp_timeout'));
	db_install_add_column ('0.8.8d', 'host', array('name' => 'snmp_sysObjectID',       'type' => 'varchar(64)',  'NULL' => false, 'default' => '',  'after' => 'snmp_sysDescr'));
	db_install_add_column ('0.8.8d', 'host', array('name' => 'snmp_sysUpTimeInstance', 'type' => 'int',          'NULL' => false, 'default' => '0', 'after' => 'snmp_sysObjectID', 'unsigned' => true));
	db_install_add_column ('0.8.8d', 'host', array('name' => 'snmp_sysContact',        'type' => 'varchar(300)', 'NULL' => false, 'default' => '',  'after' => 'snmp_sysUpTimeInstance'));
	db_install_add_column ('0.8.8d', 'host', array('name' => 'snmp_sysName',           'type' => 'varchar(300)', 'NULL' => false, 'default' => '',  'after' => 'snmp_sysContact'));
	db_install_add_column ('0.8.8d', 'host', array('name' => 'snmp_sysLocation',       'type' => 'varchar(300)', 'NULL' => false, 'default' => '',  'after' => 'snmp_sysName'));
	db_install_add_column ('0.8.8d', 'host', array('name' => 'polling_time',           'type' => 'DOUBLE',                        'default' => '0', 'after' => 'avg_time'));

	// Add realms to the admin user if it exists
	if (sizeof(db_fetch_row('SELECT * FROM user_auth WHERE id=1'))) {
		db_install_execute('0.8.8d', 'INSERT IGNORE INTO user_auth_realm VALUES (18,1)');
        db_install_execute('0.8.8d', 'INSERT IGNORE INTO user_auth_realm VALUES (20,1)');
        db_install_execute('0.8.8d', 'INSERT IGNORE INTO user_auth_realm VALUES (21,1)');
	}

	/* Aggregate Merge Changes */
	/* list all tables */
	$result = db_fetch_assoc("SHOW TABLES FROM `" . $database_default . "`");
	$tables = array();
	foreach($result as $index => $arr) {
		foreach ($arr as $t) {
			$tables[] = $t;
		}
	}

	/* V064 -> V065 tables were renamed */
	if (in_array('plugin_color_templates', $tables)) {
		db_execute("RENAME TABLE $database_default.`plugin_color_templates`  TO $database_default.`plugin_aggregate_color_templates`");
	}

	if (in_array('plugin_color_templates_item', $tables)) {
		db_execute("RENAME TABLE $database_default.`plugin_color_templates_item`  TO $database_default.`plugin_aggregate_color_template_items`");
	}

	$data = array();
	$data['columns'][] = array('name' => 'color_template_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
	$data['columns'][] = array('name' => 'name', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
	$data['primary']   = 'color_template_id';
	$data['keys'][]    = ''; # lib/plugins.php _requires_ keys!
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Color Templates';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_color_templates', $data);

	$sql[] = "INSERT IGNORE INTO `plugin_aggregate_color_templates` " .
			"(`color_template_id`, `name`) " .
			"VALUES " .
			"(1, 'Yellow: light -> dark, 4 colors'), " .
			"(2, 'Red: light yellow > dark red, 8 colors'), " .
			"(3, 'Red: light -> dark, 16 colors'), " .
			"(4, 'Green: dark -> light, 16 colors');";

	$data = array();
	$data['columns'][] = array('name' => 'color_template_item_id', 'type' => 'int(12)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
	$data['columns'][] = array('name' => 'color_template_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 'color_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 'sequence', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
	$data['primary']   = 'color_template_item_id';
	$data['keys'][]    = ''; # lib/plugins.php _requires_ keys!
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Color Items for Color Templates';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_color_template_items', $data);

	$sql[] = 'INSERT IGNORE INTO `plugin_aggregate_color_template_items` ' .
			'(`color_template_item_id`, `color_template_id`, `color_id`, `sequence`) VALUES ' .
			'(1, 1, 4, 1), (2, 1, 24, 2), (3, 1, 98, 3), (4, 1, 25, 4), ' .
			'(5, 2, 25, 1), (6, 2, 29, 2), (7, 2, 30, 3), (8, 2, 31, 4), (9, 2, 33, 5), (10, 2, 35, 6), (11, 2, 41, 7), (12, 2, 9, 8), ' .
			'(13, 3, 15, 1), (14, 3, 31, 2), (15, 3, 28, 3), (16, 3, 8, 4), (17, 3, 34, 5), (18, 3, 33, 6), (19, 3, 35, 7), (20, 3, 41, 8), ' .
			'(21, 3, 36, 9), (22, 3, 42, 10), (23, 3, 44, 11), (24, 3, 48, 12), (25, 3, 9, 13), (26, 3, 49, 14), (27, 3, 51, 15), (28, 3, 52, 16), ' .
			'(29, 4, 76, 1), (30, 4, 84, 2), (31, 4, 89, 3), (32, 4, 17, 4), (33, 4, 86, 5), (34, 4, 88, 6), (35, 4, 90, 7), (36, 4, 94, 8), ' .
			'(37, 4, 96, 9), (38, 4, 93, 10), (39, 4, 91, 11), (40, 4, 22, 12), (41, 4, 12, 13), (42, 4, 95, 14), (43, 4, 6, 15), (44, 4, 92, 16);';

	# now run all SQL commands
	if (!empty($sql)) {
		for ($a = 0; $a < count($sql); $a++) {
			$result = db_execute($sql[$a]);
		}
	}

	$result = db_fetch_assoc('SHOW TABLES FROM `' . $database_default . '`');
	$tables = array();
	foreach($result as $index => $arr) {
		foreach ($arr as $t) {
			$tables[] = $t;
		}
	}

	$data = array();
	$data['columns'][] = array('name' => 'id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
	$data['columns'][] = array('name' => 'name', 'type' => 'VARCHAR(64)', 'NULL' => false);
	$data['columns'][] = array('name' => 'graph_template_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'gprint_prefix', 'type' => 'VARCHAR(64)', 'NULL' => false);
	$data['columns'][] = array('name' => 'graph_type', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'total', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'total_type', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'total_prefix', 'type' => 'VARCHAR(64)', 'NULL' => false);
	$data['columns'][] = array('name' => 'order_type', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'created', 'type' => 'TIMESTAMP', 'NULL' => false);
	$data['columns'][] = array('name' => 'user_id', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['primary']   = 'id';
	$data['keys'][]    = array('name' => 'graph_template_id' , 'columns' => 'graph_template_id');
	$data['keys'][]    = array('name' => 'user_id' , 'columns' => 'user_id');
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Template Definitions for Aggregate Graphs';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_graph_templates', $data);

	$data = array();
	$data['columns'][] = array('name' => 'aggregate_template_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'graph_templates_item_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'sequence', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 'color_template', 'type' => 'int(11)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'item_skip', 'type' => 'CHAR(2)', 'NULL' => false);
	$data['columns'][] = array('name' => 'item_total', 'type' => 'CHAR(2)', 'NULL' => false);
	$data['primary']   = 'aggregate_template_id`,`graph_templates_item_id';
	$data['keys'][]    = '';
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Aggregate Template Graph Items';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_graph_templates_item', $data);

	$data = array();
	$data['columns'][] = array('name' => 'id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
	$data['columns'][] = array('name' => 'aggregate_template_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'template_propogation', 'type' => 'CHAR(2)', 'NULL' => false, 'default' => '');
	$data['columns'][] = array('name' => 'local_graph_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'title_format', 'type' => 'VARCHAR(128)', 'NULL' => false);
	$data['columns'][] = array('name' => 'graph_template_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'gprint_prefix', 'type' => 'VARCHAR(64)', 'NULL' => false);
	$data['columns'][] = array('name' => 'graph_type', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'total', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'total_type', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'total_prefix', 'type' => 'VARCHAR(64)', 'NULL' => false);
	$data['columns'][] = array('name' => 'order_type', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'created', 'type' => 'TIMESTAMP', 'NULL' => false);
	$data['columns'][] = array('name' => 'user_id', 'type' => 'INTEGER', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['primary']   = 'id';
	$data['keys'][]    = array('name' => 'aggregate_template_id', 'columns' => 'aggregate_template_id');
	$data['keys'][]    = array('name' => 'local_graph_id', 'columns' => 'local_graph_id');
	$data['keys'][]    = array('name' => 'title_format', 'columns' => 'title_format');
	$data['keys'][]    = array('name' => 'user_id', 'columns' => 'user_id');
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Aggregate Graph Definitions';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_graphs', $data);

	$data = array();
	$data['columns'][] = array('name' => 'aggregate_graph_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'local_graph_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'sequence', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
	$data['primary']   = 'aggregate_graph_id`,`local_graph_id';
	$data['keys'][]    = '';
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Aggregate Graph Items';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_graphs_items', $data);

	$data = array();
	$data['columns'][] = array('name' => 'aggregate_graph_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'graph_templates_item_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'sequence', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 'color_template', 'type' => 'int(11)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 'item_skip', 'type' => 'CHAR(2)', 'NULL' => false);
	$data['columns'][] = array('name' => 'item_total', 'type' => 'CHAR(2)', 'NULL' => false);
	$data['primary']   = 'aggregate_graph_id`,`graph_templates_item_id';
	$data['keys'][]    = '';
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Aggregate Graph Graph Items';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_graphs_graph_item', $data);

	/* TODO should this go in a seperate upgrade function? */
	/* Create table holding aggregate template graph params */
	$data = array();
	$data['columns'][] = array('name' => 'aggregate_template_id', 'type' => 'int(10)', 'unsigned' => 'unsigned', 'NULL' => false);
	$data['columns'][] = array('name' => 't_image_format_id', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'image_format_id', 'type' => 'tinyint(1)', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 't_height', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'height', 'type' => 'mediumint(8)', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 't_width', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'width', 'type' => 'mediumint(8)', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 't_upper_limit', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'upper_limit', 'type' => 'varchar(20)', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 't_lower_limit', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'lower_limit', 'type' => 'varchar(20)', 'NULL' => false,	'default' => 0);
	$data['columns'][] = array('name' => 't_vertical_label', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'vertical_label', 'type' => 'varchar(200)', 'default' => '');
	$data['columns'][] = array('name' => 't_slope_mode', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'slope_mode', 'type' => 'char(2)', 'default' => 'on');
	$data['columns'][] = array('name' => 't_auto_scale', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'auto_scale', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 't_auto_scale_opts', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'auto_scale_opts', 'type' => 'tinyint(1)', 'NULL' => false,	'default' => 0);
	$data['columns'][] = array('name' => 't_auto_scale_log', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'auto_scale_log', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 't_scale_log_units', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'scale_log_units', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 't_auto_scale_rigid', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'auto_scale_rigid', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 't_auto_padding', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'auto_padding', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 't_base_value', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'base_value', 'type' => 'mediumint(8)', 'NULL' => false, 'default' => 0);
	$data['columns'][] = array('name' => 't_grouping', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'grouping', 'type' => 'char(2)', 'NULL' => false, 'default' => '');
	$data['columns'][] = array('name' => 't_export', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'export', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 't_unit_value', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'unit_value', 'type' => 'varchar(20)', 'default' => '');
	$data['columns'][] = array('name' => 't_unit_exponent_value', 'type' => 'char(2)', 'default' => '');
	$data['columns'][] = array('name' => 'unit_exponent_value', 'type' => 'varchar(5)', 'NULL' => false, 'default' => '');
	$data['primary']   = 'aggregate_template_id';
	$data['keys'][]    = '';
	$data['type']      = 'MyISAM';
	$data['comment']   = 'Aggregate Template Graph Data';
	api_plugin_db_table_create ('aggregate', 'plugin_aggregate_graph_templates_graph', $data);

	/* TODO should this go in a seperate upgrade function? */
	/* Add cfed and graph_type override columns to aggregate tables */
	$columns = array();
	$columns[] = array('name' => 't_graph_type_id', 'type' => 'char(2)', 'default' => '', 'after' => 'color_template');
	$columns[] = array('name' => 'graph_type_id', 'type' => 'tinyint(3)', 'NULL' => false, 'default' => 0, 'after' => 't_graph_type_id');
	$columns[] = array('name' => 't_cdef_id', 'type' => 'char(2)', 'default' => '', 'after' => 'graph_type_id');
	$columns[] = array('name' => 'cdef_id', 'type' => 'mediumint(8)',  'unsigned' => true, 'NULL' => true, 'after' => 't_cdef_id');
	foreach(array('plugin_aggregate_graphs_graph_item', 'plugin_aggregate_graph_templates_item') as $table) {
		foreach($columns as $column) {
			api_plugin_db_add_column('aggregate', $table, $column);
		}
	}

	// Merging aggregate into mainline
	if (in_array('plugin_aggregate_color_template_items', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_color_template_items TO color_template_items');
	}

	if (in_array('plugin_aggregate_color_templates', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_color_templates TO color_templates');
	}

	if (in_array('plugin_aggregate_graph_templates', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_graph_templates TO aggregate_graph_templates');
	}

	if (in_array('plugin_aggregate_graph_templates_graph', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_graph_templates_graph TO aggregate_graph_templates_graph');
	}

	if (in_array('plugin_aggregate_graph_templates_item', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_graph_templates_item TO aggregate_graph_templates_item');
	}

	if (in_array('plugin_aggregate_graphs', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_graphs TO aggregate_graphs');
	}

	if (in_array('plugin_aggregate_graphs_graph_item', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_graphs_graph_item TO aggregate_graphs_graph_item');
	}

	if (in_array('plugin_aggregate_graphs_items', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_aggregate_graphs_items TO aggregate_graphs_items');
	}

	$id = db_fetch_cell("SELECT * FROM plugin_realms WHERE plugin='aggregate'");
	if (!empty($id)) {
		db_execute('UPDATE IGNORE user_auth_realm SET realm_id=5 WHERE realm_id=' . (100 + $id));
		db_execute('DELETE FROM user_auth_realm WHERE realm_id=' . (100 + $id));
	}

	db_install_execute('0.8.8d', "DELETE FROM plugin_config WHERE directory='aggregate'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_realms WHERE plugin='aggregate'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_db_changes WHERE plugin='aggregate'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_hooks WHERE plugin='aggregate'");

	if (!in_array('plugin_autom8_match_rule_items', $tables)) {
		$data = array();
		$data['columns'][] = array('name' => 'id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
		$data['columns'][] = array('name' => 'rule_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'rule_type', 'type' => 'smallint(3)',  'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'sequence', 'type' => 'smallint(3)',  'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'operation', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'field', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['columns'][] = array('name' => 'operator', 'type' => 'smallint(3)',  'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'pattern', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['primary'] = 'id';
		$data['keys'][] = '';
		$data['type'] = 'MyISAM';
		$data['comment'] = 'Automation Match Rule Items';
		api_plugin_db_table_create ('autom8', 'plugin_autom8_match_rule_items', $data);

		$sql[] = "INSERT IGNORE INTO `plugin_autom8_match_rule_items` 
			(`id`, `rule_id`, `rule_type`, `sequence`, `operation`, `field`, `operator`, `pattern`) 
			VALUES 
			(1, 1, 1, 1, 0, 'h.description', 14, ''), 
			(2, 1, 1, 2, 1, 'h.snmp_version', 12, '2'),
			(3, 1, 3, 1, 0, 'ht.name', 1, 'Linux'),
			(4, 2, 1, 1, 0, 'ht.name', 1, 'Linux'),
			(5, 2, 1, 2, 1, 'h.snmp_version', 12, '2'),
			(6, 2, 3, 1, 0, 'ht.name', 1, 'SNMP'),
			(7, 2, 3, 2, 1, 'gt.name', 1, 'Traffic')";
	}else{
		$sql[] = "UPDATE plugin_autom8_match_rules SET field=REPLACE(field, 'host_template.', 'ht.')";
		$sql[] = "UPDATE plugin_autom8_match_rules SET field=REPLACE(field, 'host.', 'h.')";
		$sql[] = "UPDATE plugin_autom8_match_rules SET field=REPLACE(field, 'graph_templates.', 'gt.')";
		$sql[] = "UPDATE plugin_autom8_match_rules SET field=REPLACE(field, 'graph_templates_graph.', 'gtg.')";
	}

	if (!in_array('plugin_autom8_graph_rules', $tables)) {
		$data = array();
		$data['columns'][] = array('name' => 'id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
		$data['columns'][] = array('name' => 'name', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['columns'][] = array('name' => 'snmp_query_id', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'graph_type_id', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'enabled', 'type' => 'char(2)', 'NULL' => true,  'default' => '');
		$data['primary'] = 'id';
		$data['keys'][] = '';
		$data['type'] = 'MyISAM';
		$data['comment'] = 'Automation Graph Rules';
		api_plugin_db_table_create ('autom8', 'plugin_autom8_graph_rules', $data);

		$sql[] = "INSERT IGNORE INTO `plugin_autom8_graph_rules`
			(`id`, `name`, `snmp_query_id`, `graph_type_id`, `enabled`)
			VALUES 
			(1, 'Traffic 64 bit Server', 1, 14, ''),
			(2, 'Traffic 64 bit Server Linux', 1, 14, ''),
			(3, 'Disk Space', 8, 18, '')";
	}

	if (!in_array('plugin_autom8_graph_rule_items', $tables)) {
		$data = array();
		$data['columns'][] = array('name' => 'id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
		$data['columns'][] = array('name' => 'rule_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'sequence', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'operation', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'field', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['columns'][] = array('name' => 'operator', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'pattern', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['primary'] = 'id';
		$data['keys'][] = '';
		$data['type'] = 'MyISAM';
		$data['comment'] = 'Automation Graph Rule Items';
		api_plugin_db_table_create ('autom8', 'plugin_autom8_graph_rule_items', $data);

		$sql[] = "INSERT IGNORE INTO `plugin_autom8_graph_rule_items`
			(`id`, `rule_id`, `sequence`, `operation`, `field`, `operator`, `pattern`)
			VALUES
			(1, 1, 1, 0, 'ifOperStatus', 7, 'Up'),
			(2, 1, 2, 1, 'ifIP', 16, ''),
			(3, 1, 3, 1, 'ifHwAddr', 16, ''),
			(4, 2, 1, 0, 'ifOperStatus', 7, 'Up'),
			(5, 2, 2, 1, 'ifIP', 16, ''),
			(6, 2, 3, 1, 'ifHwAddr', 16, '')";
	}

	if (!in_array('plugin_autom8_tree_rules', $tables)) {
		$data = array();
		$data['columns'][] = array('name' => 'id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
		$data['columns'][] = array('name' => 'name', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['columns'][] = array('name' => 'tree_id', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'tree_item_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'leaf_type', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'host_grouping_type', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'rra_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'enabled', 'type' => 'char(2)', 'NULL' => true,  'default' => '');
		$data['primary'] = 'id';
		$data['keys'][] = '';
		$data['type'] = 'MyISAM';
		$data['comment'] = 'Automation Tree Rules';
		api_plugin_db_table_create ('autom8', 'plugin_autom8_tree_rules', $data);

		$sql[] = "INSERT IGNORE INTO `plugin_autom8_tree_rules`
			(`id`, `name`, `tree_id`, `tree_item_id`, `leaf_type`, `host_grouping_type`, `rra_id`, `enabled`)
			VALUES
			(1, 'New Device', 1, 0, 3, 1, 0, ''),
			(2, 'New Graph',  1, 0, 2, 0, 1, '')";
	}

	if (!in_array('plugin_autom8_tree_rule_items', $tables)) {
		$data = array();
		$data['columns'][] = array('name' => 'id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'auto_increment' => true);
		$data['columns'][] = array('name' => 'rule_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'sequence', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'field', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['columns'][] = array('name' => 'rra_id', 'type' => 'mediumint(8)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'sort_type', 'type' => 'smallint(3)', 'unsigned' => 'unsigned', 'NULL' => false, 'default' => 0);
		$data['columns'][] = array('name' => 'propagate_changes', 'type' => 'char(2)', 'NULL' => true, 'default' => '');
		$data['columns'][] = array('name' => 'search_pattern', 'type' => 'varchar(255)', 'NULL' => false, 'default' => '');
		$data['columns'][] = array('name' => 'replace_pattern',	'type' => 'varchar(255)',                       	    'NULL' => false, 	'default' => '');
		$data['primary'] = 'id';
		$data['keys'][] = '';
		$data['type'] = 'MyISAM';
		$data['comment'] = 'Automation Tree Rule Items';
		api_plugin_db_table_create ('autom8', 'plugin_autom8_tree_rule_items', $data);

	$sql[] = "INSERT INTO `plugin_autom8_tree_rule_items`
		(`id`, `rule_id`, `sequence`, `field`, `rra_id`, `sort_type`, `propagate_changes`, `search_pattern`, `replace_pattern`)
		VALUES
		(1, 1, 1, 'ht.name', 0, 1, '', '^(.*)\\\\s*Linux\\\\s*(.*)$', '$\{1\}\\\\n$\{2\}'),
		(2, 1, 2, 'h.hostname', 0, 1, '', '^(\\\\w*)\\\\s*(\\\\w*)\\\\s*(\\\\w*).*$', '$\{1\}\\\\n$\{2\}\\\\n$\{3\}'),
		(3, 2, 1, '0', 0, 2, 'on', 'Traffic', ''),
		(4, 2, 2, 'gtg.title_cache', 0, 1, '', '^(.*)\\\\s*-\\\\s*Traffic -\\\\s*(.*)$', '$\{1\}\\\\n$\{2\}');";
	}else{
		$sql[] = "UPDATE plugin_autom8_tree_rule_items SET field=REPLACE(field, 'host_template.', 'ht.')";
		$sql[] = "UPDATE plugin_autom8_tree_rule_items SET field=REPLACE(field, 'host.', 'h.')";
		$sql[] = "UPDATE plugin_autom8_tree_rule_items SET field=REPLACE(field, 'graph_templates.', 'gt.')";
		$sql[] = "UPDATE plugin_autom8_tree_rule_items SET field=REPLACE(field, 'graph_templates_graph.', 'gtg.')";
	}

	# now run all SQL commands
	if (!empty($sql)) {
		for ($a = 0; $a < count($sql); $a++) {
			$result = db_execute($sql[$a]);
		}
	}

	/* autom8 table renames */
	$autom8_tables = array(
		'plugin_autom8_graph_rules', 
		'plugin_autom8_graph_rule_items', 
		'plugin_autom8_match_rule_items', 
		'plugin_autom8_tree_rules', 
		'plugin_autom8_tree_rule_items'
	);

	foreach($autom8_tables as $table) {
		if (in_array($table, $tables)) {
			db_install_execute('0.8.8d', "RENAME TABLE $table TO " . str_replace('plugin_autom8', 'automation', $table));
		}
	}

	$id = db_fetch_cell("SELECT * FROM plugin_realms WHERE plugin='autom8'");
	if (!empty($id)) {
		db_execute('UPDATE IGNORE user_auth_realm SET realm_id=23 WHERE realm_id=' . (100 + $id));
		db_execute('DELETE FROM user_auth_realm WHERE realm_id=' . (100 + $id));
	}

	db_install_execute('0.8.8d', "DELETE FROM plugin_config WHERE directory='autom8'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_realms WHERE plugin='autom8'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_db_changes WHERE plugin='autom8'");
	db_install_execute('0.8.8d', "DELETE FROM plugin_hooks WHERE plugin='autom8'");

	db_install_execute('0.8.8d', "UPDATE settings SET name=REPLACE(name, 'autom8', 'automation') WHERE name LIKE 'autom8%'");

	// migrate discovery to Core if exists
	if (in_array('plugin_discover_hosts', $tables)) {
		db_install_execute('0.8.8d', 'RENAME TABLE plugin_discover_hosts TO automation_devices');
		db_install_execute('0.8.8d', "ALTER TABLE automation_devices 
			ADD COLUMN network_id INT unsigned NOT NULL default '0' FIRST, 
			ADD INDEX network_id(network_id)");

		if (in_array('plugin_discover_processes', $tables)) {
			db_install_execute('0.8.8d', 'RENAME TABLE plugin_discover_processes TO automation_processes');
		}

		if (in_array('plugin_discover_template', $tables)) {
			db_install_execute('0.8.8d', 'RENAME TABLE plugin_discover_template TO automation_templates');
			db_install_execute('0.8.8d', "ALTER TABLE automation_templates 
				ADD COLUMN sysname VARCHAR(255) NOT NULL default '' AFTER sysdescr, 
				ADD COLUMN sysoid VARCHAR(60) NOT NULL default '' AFTER sysname, 
				ADD COLUMN sequence INT UNSIGNED default '0' AFTER sysoid");
			db_install_execute('0.8.8d', "UPDATE automation_templates SET sequence=id");
		}
	}

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `automation_devices` (
		`network_id` int(10) unsigned NOT NULL DEFAULT '0',
		`hostname` varchar(100) NOT NULL DEFAULT '',
		`ip` varchar(17) NOT NULL DEFAULT '',
		`hash` varchar(12) NOT NULL DEFAULT '',
		`community` varchar(100) NOT NULL DEFAULT '',
		`snmp_version` tinyint(1) unsigned NOT NULL DEFAULT '1',
		`snmp_username` varchar(50) DEFAULT NULL,
		`snmp_password` varchar(50) DEFAULT NULL,
		`snmp_auth_protocol` char(5) DEFAULT '',
		`snmp_priv_passphrase` varchar(200) DEFAULT '',
		`snmp_priv_protocol` char(6) DEFAULT '',
		`snmp_context` varchar(64) DEFAULT '',
		`sysName` varchar(100) NOT NULL DEFAULT '',
		`sysLocation` varchar(255) NOT NULL DEFAULT '',
		`sysContact` varchar(255) NOT NULL DEFAULT '',
		`sysDescr` varchar(255) NOT NULL DEFAULT '',
		`sysUptime` int(32) NOT NULL DEFAULT '0',
		`os` varchar(64) NOT NULL DEFAULT '',
		`snmp` tinyint(4) NOT NULL DEFAULT '0',
		`known` tinyint(4) NOT NULL DEFAULT '0',
		`up` tinyint(4) NOT NULL DEFAULT '0',
		`time` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`ip`),
		KEY `hostname` (`hostname`)) 
		ENGINE=MyISAM 
		COMMENT='Table of Discovered Devices'");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `automation_networks` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name for this network',
		`subnet_range` varchar(255) NOT NULL DEFAULT '' COMMENT 'Defined subnet ranges for discovery',
		`dns_servers` varchar(128) NOT NULL DEFAULT '' COMMENT 'DNS Servers to use for name resolution',
		`enabled` char(2) DEFAULT '',
		`snmp_id` int(10) unsigned DEFAULT NULL,
		`up_hosts` int(10) unsigned NOT NULL DEFAULT '0',
		`snmp_hosts` int(10) unsigned NOT NULL DEFAULT '0',
		`ping_method` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The ping method (ICMP:TCP:UDP)',
		`ping_port` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'For TCP:UDP the port to ping',
		`ping_timeout` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The ping timeout in seconds',
		`sched_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Schedule type: manual or automatic',
		`day_of_week` varchar(45) DEFAULT NULL COMMENT 'The days of week to run in crontab format',
		`month` varchar(45) DEFAULT NULL COMMENT 'The months to run in crontab format',
		`day_of_month` varchar(45) DEFAULT NULL COMMENT 'The days of month to run in crontab format',
		`hour` varchar(45) DEFAULT NULL COMMENT 'The hours to run in crontab format',
		`min` varchar(45) DEFAULT NULL COMMENT 'The minutes to run in crontab format',
		`run_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The maximum runtime for the discovery',
		`last_runtime` double NOT NULL DEFAULT '0' COMMENT 'The last runtime for discovery',
		`last_started` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the discovery last started',
		`last_status` varchar(128) NOT NULL DEFAULT '' COMMENT 'The last exit message if any',
		`rerun_data_queries` char(2) DEFAULT NULL COMMENT 'Rerun data queries or not for existing hosts',
		PRIMARY KEY (`id`)) 
		ENGINE=MyISAM 
		COMMENT='Stores scanning subnet definitions'");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `automation_processes` (
		`pid` int(8) unsigned NOT NULL,
		`taskname` varchar(20) NOT NULL DEFAULT '',
		`taskid` int(10) unsigned NOT NULL DEFAULT '0',
		`heartbeat` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (`pid`)) 
		ENGINE=MEMORY 
		COMMENT='Table required for parallelization of data collection'");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `automation_snmp` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`name` varchar(100) NOT NULL DEFAULT '',
		PRIMARY KEY (`id`)) 
		ENGINE=MyISAM 
		COMMENT='Group of SNMP Option Sets'");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `automation_snmp_items` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`snmp_id` int(10) unsigned NOT NULL DEFAULT '0',
		`sequence` int(10) unsigned NOT NULL DEFAULT '0',
		`snmp_version` varchar(100) NOT NULL DEFAULT '',
		`snmp_readstring` varchar(100) NOT NULL,
		`snmp_port` int(10) NOT NULL DEFAULT '161',
		`snmp_timeout` int(10) unsigned NOT NULL DEFAULT '500',
		`snmp_retries` tinyint(11) unsigned NOT NULL DEFAULT '3',
		`max_oids` int(12) unsigned DEFAULT '10',
		`snmp_username` varchar(50) DEFAULT NULL,
		`snmp_password` varchar(50) DEFAULT NULL,
		`snmp_auth_protocol` char(5) DEFAULT '',
		`snmp_priv_passphrase` varchar(200) DEFAULT '',
		`snmp_priv_protocol` char(6) DEFAULT '',
		`snmp_context` varchar(64) DEFAULT '',
		PRIMARY KEY (`id`,`snmp_id`)) 
		ENGINE=MyISAM 
		COMMENT='Set of SNMP Options'");

	db_install_execute('0.8.8d', "CREATE TABLE IF NOT EXISTS `automation_templates` (
		`id` int(8) NOT NULL AUTO_INCREMENT,
		`host_template` int(8) NOT NULL DEFAULT '0',
		`tree` int(12) NOT NULL DEFAULT '0',
		`snmp_version` tinyint(3) NOT NULL DEFAULT '0',
		`sysdescr` varchar(255) NOT NULL DEFAULT '',
		`sysname` varchar(255) NOT NULL DEFAULT '',
		`sysoid` varchar(60) NOT NULL DEFAULT '',
		`sequence` int(10) unsigned DEFAULT '0',
		PRIMARY KEY (`id`)) 
		ENGINE=MyISAM 
		COMMENT='Templates of SysDescr SysName and SysOID matches to use to automation'");
}
