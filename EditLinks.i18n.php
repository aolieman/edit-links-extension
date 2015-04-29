<?php
/* 
 * Edit links extension for MediaWiki
 * Copyright (C) 2009  Alex Olieman
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

$messages = array ();

$messages['en'] = array (
	'editlinks'                  => 'Edit links',
	'editlinks-desc'             => 'Edits links on a local page',
	# Group entry defaults
	'group-import-wizard'        => 'Import wizards',
	'group-import-wizard-member' => 'Import wizard',
	'grouppage-import-wizard'    => 'Special:ImportWizard',
	# Log entry
	'import-logentry-wizard'        => 'edited the links of [[$1]]',
	'import-logentry-wizard-detail' => 'from [$1 $2]',
	# Common elements
	'button-prev' => '< Back',
	'button-next' => 'Next >',
	# Errors
	'iw-nosourcetitleset'   => 'No source title was set. Please set a source title.',
	'iw-nolocalsource'      => 'This page does not exist in Future Planet Wiki.',
	# Page 1
	'iw-explain-page1'         => 'Enter the name of a (previously imported) local page of which you want to edit the links. Select an external wiki for interwiki transclusion of link targets. You will have options to transclude, unlink and delete the links and to upload missing images.',
	'iw-source-title'          => 'Local article title:',
	'iw-source-wiki'           => 'Wiki for transclusion:',
	'iw-source-options'        => 'Source options:',
	'iw-option-expandtemplate' => 'Expand templates',
	'iw-option-followredirect' => 'Follow redirections',
	'iw-link-number'           => 'Link #:',
	'iw-link-content'          => 'Content of link:',
	'iw-transclude'            => 'Transclude',
	'iw-unlink'                => 'Unlink',
	'iw-delete'                => 'Delete',
	# Page 2
	'iw-explain-page2' => 'This is a list of links on the source page. Edits to the links will be saved when you click next.',
	# Page 3
	'iw-viewarticle'        => 'View $1',
);
?>