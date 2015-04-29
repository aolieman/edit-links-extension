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


class EditLinksPage extends SpecialPage
{
	public function __construct ()
	{
		wfLoadExtensionMessages ('EditLinks');
		SpecialPage::__construct ('EditLinks', 'import-wizard');
	}
	
	public function isRestricted () { return false; }
	
	public function execute ($par)
	{
		global $IP, $wgJsMimeType, $wgOut, $wgRequest, $wgScriptPath, $wgStyleVersion, $wgUser;
		
		if (!$this->userCanExecute ($wgUser))
			return $this->displayRestrictionError ();
		
		$this->outputHeader ();
		
		$this->setHeaders ();
		
		$scriptFile = str_replace ($IP, $wgScriptPath, dirname (__FILE__) . '/EditLinks.js');
		$styleFile = str_replace ($IP, $wgScriptPath, dirname (__FILE__) . '/EditLinks.css');
		$wgOut->addScript( "<script type=\"{$wgJsMimeType}\" src=\"{$scriptFile}?$wgStyleVersion\"></script>\n");
		$wgOut->addLink (array ('rel' => 'stylesheet', 'href' => $styleFile));
		
		if ($wgRequest->getCheck ('iwPage3'))
			$this->showSaveForm ();
		elseif ($wgRequest->getCheck ('iwPage2'))
			$this->showLinksForm ();
		else
			$this->showImportForm ($par);
	}
	
	/**
	 * Shows the initial import form, where the user can select the page he wants to import, as well as the source from which to import.
	 */
	private function showImportForm ($pageName)
	{
		global $iwSources, $wgLogActions, $wgOut, $wgRequest;
		
		$wgOut->addWikiText (wfMsg ('iw-explain-page1'));
				
		$form  = Xml::openElement ('form', array ('method' => 'post', 'action' => SpecialPage::getTitle()->getLocalURL()));
		$form .= Xml::openElement ('table', array ('width' => '100%'));
		$form .= Xml::openElement ('tr');
		$form .= Xml::tags ('td', array ('width' => '200px'), Xml::label (wfMsg ('iw-source-title'), 'iwSourceTitle'));
		$form .= Xml::tags ('td', NULL, Xml::input ('iwSourceTitle', 50, $wgRequest->getVal ('iwSourceTitle', $pageName), array ('id' => 'iwSourceTitle')));
		$form .= Xml::closeElement ('tr');
		$form .= Xml::openElement ('tr');
		$form .= Xml::tags ('td', array ('width' => '200px'), Xml::label (wfMsg ('iw-source-wiki'), 'iwSourceWiki'));
		$form .= Xml::openElement ('td');
		$form .= Xml::openElement ('select', array ('name' => 'iwSourceWiki', 'id' => 'iwSourceWiki'));
		foreach ($iwSources as $sourceId => $sourceInfo)
			$form .= Xml::option ($sourceInfo['name'], $sourceId, ($wgRequest->getVal ('iwSourceWiki') == $sourceId));
		$form .= Xml::closeElement ('select');
		$form .= Xml::closeElement ('td');
		$form .= Xml::closeElement ('tr');
		$form .= Xml::closeElement ('table');
		$form .= Xml::submitButton (wfMsg ('button-prev'), array ('disabled' => 'disabled'));
		$form .= Xml::submitButton (wfMsg ('button-next'), array ('name' => 'iwPage2'));
		$form .= Xml::closeElement ('form');
		
		$wgOut->addHTML ($form);
	}
	
	/**
	 * Shows the links on the local page. Should offer options to copy from the source wiki, interwiki link to source wiki or unlink.
	 */
	private function showLinksForm ()
	{
		global $iwSources, $wgOut, $wgRequest, $wgTitle;
		static $iwImportCacheLocal = array ();
		
		$sourceWiki = $wgRequest->getVal ('iwSourceWiki');
		$sourceTitle = $wgRequest->getVal ('iwSourceTitle');						
			
		if (empty ($sourceTitle))
			return $this->showError ('iw-nosourcetitleset', 'iwPage1');
			
		$articleBody = self::getLocalArticle ($sourceTitle, $wgRequest->getCheck ('iwExpandTemplates'), $wgRequest->getCheck ('iwFollowRedirects'));
				
		$wgOut->addWikiText (wfMsg ('iw-explain-page2'));
		
		

		$form  = Xml::openElement ('form', array ('method' => 'post', 'action' => SpecialPage::getTitle()->getLocalURL()));
		$form .= Xml::openElement ('table', array ('width' => '100%'));
		$form .= Xml::hidden ('iwSourceWiki', $sourceWiki);
		$form .= Xml::hidden ('iwSourceTitle', $sourceTitle);
		$form .= Xml::openElement ('tr');
		$form .= Xml::tags ('td', NULL, Xml::label (wfMsg ('iw-link-number'), 'iwLinkNumber'));
		$form .= Xml::tags ('td', NULL, Xml::label (wfMsg ('iw-link-content'), 'iwLinkContent'));	
		$form .= Xml::tags ('td', NULL, Xml::label (wfMsg ('iw-transclude'), 'iwTransclude'));
		$form .= Xml::tags ('td', NULL, Xml::label (wfMsg ('iw-unlink'), 'iwUnlink'));
		$form .= Xml::tags ('td', NULL, Xml::label (wfMsg ('iw-delete'), 'iwDelete'));		
		$form .= Xml::closeElement ('tr');

		$links = array();
		$content = array();
		$i = 0;
		$cursor = 0;
		$linkOffset = 0;
		$brackets = 0;
		do
		{
			$linkOffset = strpos($articleBody, '[[', $linkOffset);
			$contentLength = $linkOffset - $cursor;
			$brackets = $brackets + 1;
			$linkStart = $linkOffset;
			
			while($brackets!=0)
			{
			
			$linkEnd = strpos($articleBody, ']]', $linkOffset);
			
			$linkOffset = strpos($articleBody, '[[', $linkOffset+2);

			if($linkOffset<$linkEnd)
			{
			$linkOffset = $linkEnd+2;
			$linkEnd = strpos($articleBody, ']]', $linkOffset);
			}
			else 
			{
			$brackets = $brackets - 1;
			}
			}
	
			
			$linkLength = $linkEnd - $linkStart +2;
			
			$links[$i] = array(substr($articleBody, $linkStart, $linkLength));
			$content[$i] = array(substr($articleBody, $cursor, $contentLength));
			$cursor = $linkEnd + 2;
			
			$form .= Xml::openElement ('tr');
			
			$form .= Xml::openElement ('td');
			$form .= wfMsg ($i);
			$form .= Xml::closeElement ('td');

			$form .= Xml::openElement ('td');
			$form .= Xml::input ("iwLinks[$i]", 80, $links[$i][0], array ('id' => "iwLinks[$i]"));
			$form .= Xml::closeElement ('td');
			
			$form .= Xml::openElement ('td');
			$form .= Xml::check ("iwTransclude[$i]", false, array ('id' => "iwTransclude[$i]", 'value' => "1"));			
			$form .= Xml::closeElement ('td');

			$form .= Xml::openElement ('td');
			$form .= Xml::check ("iwUnlink[$i]", false, array ('id' => "iwUnlink[$i]", 'value' => "1"));			
			$form .= Xml::closeElement ('td');

			$form .= Xml::openElement ('td');
			$form .= Xml::check ("iwDelete[$i]", false, array ('id' => "iwDelete[$i]", 'value' => "1"));			
			$form .= Xml::closeElement ('td');

			$form .= Xml::closeElement ('tr');
			$form .= Xml::hidden ("iwContent[$i]", $content[$i][0], array ('id' => "iwContent[$i]"));
			$i++;
		} while ( $linkOffset != strrpos($articleBody, '[[') );
		
		$appendix = substr($articleBody, $linkEnd + 3, strlen($articleBody));

		$form .= Xml::hidden ('iwNumberOfLinks', $i);
		$form .= Xml::hidden ('iwAppendix', $appendix);
		
		$form .= Xml::closeElement ('table');
		$form .= Xml::submitButton (wfMsg ('button-prev'), array ('name' => 'iwPage1'));
		$form .= Xml::submitButton (wfMsg ('button-next'), array ('name' => 'iwPage3'));
		$form .= Xml::closeElement ('form');
	
		
		
		$wgOut->addHTML ($form);
		
	}
	
	/**
	 * Show the form where the user can set options to use when saving the imported article.
	 */
	private function showSaveForm ()
	{
		global $iwSources, $wgOut, $wgRequest, $wgUser;
		
		$sourceWiki = $wgRequest->getVal ('iwSourceWiki');
		$sourceTitle = $wgRequest->getVal ('iwSourceTitle');
		$content = $wgRequest->getArray ('iwContent');
		$links = $wgRequest->getArray ('iwLinks');
		$transclude = $wgRequest->getArray ('iwTransclude');
		$unlink = $wgRequest->getArray ('iwUnlink');
		$delete = $wgRequest->getArray ('iwDelete');
		$numberOfLinks = $wgRequest->getVal ('iwNumberOfLinks');
		$appendix = $wgRequest->getVal ('iwAppendix');
		$articleTitle = Title::newFromText ($sourceTitle);
		
		
		for ($i = 0; $i < $numberOfLinks; $i++)
		{
			$divider = strpos ($links[$i], "|");

			# Transclude
			if ($transclude[$i] == "1")
			{
			$title = trim($links[$i], "[]");
			if ($divider > 0)
				$title = substr ($title, 0, $divider-2);
			self::transcludeArticle ($title);
			}

			# Unlink (should perhaps be private function)
			if ($unlink[$i] == "1")
			{
			if ($devider > 0)
				$links[$i] = substr ($links[$i], $divider+1);
			$links[$i] = trim($links[$i], "[]");
			}
			
			# Delete
			if ($delete[$i] == "1")
				$links[$i] = "";

			
		}

		
		
		# Interweave content and links, then saves.
		for ($i = 0; $i < $numberOfLinks; $i++)
		{
		$articleText .= $content[$i];
		$articleText .= $links[$i];
		}
		$articleText .= $appendix;
		
		$articleFlags =  EDIT_UPDATE | EDIT_DEFER_UPDATES | EDIT_AUTOSUMMARY | EDIT_SUPPRESS_RC;
		$article = new Article ($articleTitle);
		$article->doEdit ($articleText, 'edited links', $articleFlags);


		
		$log = new LogPage ('import');
		$log->addEntry ('wizard', $articleTitle, '');
		
		$wgOut->addHTML (Xml::element ('p', NULL, wfMsg ('importsuccess')));
		$wgOut->addHTML (Xml::tags ('p', NULL, wfMsg ('iw-viewarticle', $wgUser->getSkin()->makeLinkObj ($articleTitle))));
		$wgOut->addReturnTo (SpecialPage::getTitle());
		$wgOut->returnToMain ();
	}
	
	private function showError ($errorMsg, $prev = 'disabled', $next = 'disabled')
	{
		global $wgOut, $wgRequest;
		
		$sourceWiki = $wgRequest->getVal ('iwSourceWiki');
		$sourceTitle = $wgRequest->getVal ('iwSourceTitle');
		$destTitle = $wgRequest->getVal ('iwDestTitle');
		
		$form  = Xml::openElement ('form', array ('method' => 'post', 'action' => SpecialPage::getTitle()->getLocalURL()));
		$form .= Xml::hidden ('iwSourceWiki', $sourceWiki);
		$form .= Xml::hidden ('iwSourceTitle', $sourceTitle);
		$form .= Xml::hidden ('iwDestTitle', $destTitle);
		$form .= Xml::tags ('p', NULL, wfMsg ($errorMsg));
		$form .= Xml::submitButton (wfMsg ('button-prev'), ($prev == 'disabled') ? array ('disabled' => 'disabled') : array ('name' => $prev));
		$form .= Xml::submitButton (wfMsg ('button-next'), ($next == 'disabled') ? array ('disabled' => 'disabled') : array ('name' => $next));
		$form .= Xml::closeElement ('form');
		
		$wgOut->addHTML ($form);
	}
	
	/**
	 * Function to transclude a given article from an external wiki.
	 * 
	 * @param string $title The title of the article to import.
	 */

	private static function transcludeArticle ($title)
	{
		global $iwSources, $wgOut, $wgRequest;

		$sourceWiki = $wgRequest->getVal ('iwSourceWiki');
		$articleText = "{{transcluded from|".$sourceWiki."}}{{".$sourceWiki."::".$title."}}";
		$articleFlags =  EDIT_NEW | EDIT_DEFER_UPDATES | EDIT_AUTOSUMMARY;
		$articleTitle = Title::newFromText ($title);
		$article = new Article ($articleTitle);
		$article->doEdit ($articleText, 'interwiki transclusion', $articleFlags);
	}

	/**
	 * Function to download the body of a given article.
	 *
	 * @param string $title The title of the article to import.
	 * @param string $source The key of the wiki to import from.
	 * @param bool $expandTemplate Whether to expand templates in the article, or leave them as is. (default is true)
	 * @param bool $followRedirects Whether to follow redirects when importing the article. (default is true)
	 * @returns string The body of article in WikiText.
	 */
	private static function getArticleBody ($title, $source, $expandTemplate = true, $followRedirects = true)
	{
		global $iwSources;
		static $iwImportCache = array ();
		
		$exportTitle = self::escapeTitle ($title);
		
		$link = str_replace ('$1', $exportTitle, $iwSources[$source]['url']);
		$link .= ($expandTemplate) ? '&action=raw&templates=expand' : '&action=raw';
		
		if (empty ($aiImportCache[$link]))
		{
			$iwImportCache[$link] = Http::request ('GET', $link);
			// Trim the article for matching it later.
			$iwImportCache[$link] = trim ($iwImportCache[$link]);
		}
		
		if ($iwImportCache[$link] === false)
			return false;
		if (empty ($iwImportCache))
			return false;
		
		if (preg_match ('!^#REDIRECT \[\[(.+)\]\]$!', $iwImportCache[$link], $results) && $followRedirects)
			return self::getArticleBody ($results[1], $source, $expandTemplate, $followRedirects);
		
		return $iwImportCache[$link];
	}

	
	/**
	 * Function to download the body of the local article.
	 *
	 * @param string $title The title of the article to import.
	 * @param bool $expandTemplate Whether to expand templates in the article, or leave them as is. (default is true)
	 * @param bool $followRedirects Whether to follow redirects when importing the article. (default is true)
	 * @returns string The body of article in WikiText.
	 */
	private static function getLocalArticle ($title, $expandTemplate = true, $followRedirects = true)
	{
		global $iwSources;
		static $iwImportCacheLocal = array ();
		
		$exportTitle = self::escapeTitle ($title);
		
		$link = str_replace ('$1', $exportTitle, 'http://www.wikid.eu/index.php?title=$1'); #should automatically use local URL
		$link .= ($expandTemplate) ? '&action=raw&templates=expand' : '&action=raw';
		
		if (empty ($aiImportCacheLocal[$link]))
		{
			$iwImportCacheLocal[$link] = Http::request ('GET', $link);
			// Trim the article for matching it later.
			$iwImportCacheLocal[$link] = trim ($iwImportCacheLocal[$link]);
		}
		
		if ($iwImportCacheLocal[$link] === false)
			return false;
		if (empty ($iwImportCacheLocal))
			return false;
		
		if (preg_match ('!^#REDIRECT \[\[(.+)\]\]$!', $iwImportCacheLocal[$link], $results) && $followRedirects)
			return self::getArticleBody ($results[1], $expandTemplate, $followRedirects);
		
		return $iwImportCacheLocal[$link];
	}
	
	private static function escapeTitle ($title)
	{
		$title = str_replace (' ', '_', $title);
		$title = wfUrlencode ($title) ;
		return $title;
	}
}
?>
