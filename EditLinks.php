<?php
/* 
 * Edit links extension for MediaWiki
 * Copyright (C) 2009  Alex Olieman and Roel Obdam
 * It was based on Import Wizard by Raymond Jelierse
 * http://code.google.com/p/import-wizard-extension/
 *	
 * This extension is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *	
 * This extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *	
 * You should have received a copy of the GNU General Public License along
 * with this extension; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

if (!defined ('MEDIAWIKI'))
	die ();

// Credits
$wgExtensionCredits['specialpage'][] = array (
	'name'           => 'Edit links',
	'author'         => '[http://www.futureplanetwiki.org/wiki/User:Alex Alex&nbsp;Olieman] and Roel Obdam',
	'version'        => '0.3.1 (2009-29-07)',
	'url'            => 'http://futureplanetwiki.org/wiki/Future_Planet_Wiki:Extensions',
	'descriptionmsg' => 'editlinks-desc',
);

// Added user right and corresponding group
$wgAvailableRights[] = 'import-wizard';
$wgGroupPermissions['import-wizard']['import-wizard'] = true;

// Log entry
$wgLogActions['import/wizard'] = 'import-logentry-wizard';

// Localisation
$wgExtensionMessagesFiles['EditLinks'] = dirname( __FILE__ ) . '/EditLinks.i18n.php';

// Special page class
$wgSpecialPages['EditLinks'] = 'EditLinksPage';
$wgSpecialPageGroups['EditLinks'] = 'other';
$wgAutoloadClasses['EditLinksPage'] = dirname( __FILE__ ) . '/EditLinks.body.php';
#$wgSpecialPages['EditLinks'] = 'ImportWizardPage';
#$wgSpecialPageGroups['EditLinks'] = 'other';
#$wgAutoloadClasses['EditLinksPage'] = dirname( __FILE__ ) . '/EditLinks.body.php';

// Settings initialisation
if (empty ($iwSources)) $iwSources = array (
	'wikipedia' => array ('name' => 'Wikipedia (English)', 'url' => 'http://en.wikipedia.org/w/index.php?title=$1', 'interwiki' => 'wikipedia:'),
	'docuwiki' => array ('name' => 'DocuWiki', 'url' => 'http://www.docuwiki.net/index.php?title=$1', 'interwiki' => 'docuwiki:'),
	'wikid' => array ('name' => 'WikID', 'url' => 'http://wikid.eu/index.php/$1', 'interwiki' => 'wikid:'),
);

?>