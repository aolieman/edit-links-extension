# Edit Links #

The Edit links extension adds extra functionality to [MediaWiki](http://www.mediawiki.org) for editing links on local pages. It was written to complement the [Import Wizard extention](http://code.google.com/p/import-wizard-extension/) by Raymond Jelierse.

Enter the name of a (previously imported) local page of which you want to edit the links. Select an external wiki for interwiki transclusion of link targets. You will have options to transclude, unlink and delete the links and to upload missing images.

## Features ##
  * (Import an article from a specified list of sources, using the import wizard.)
  * Use the imported page as source for Edit links.
  * It generates an editable list of all internal links on the page.
  * Offers checkboxes to transclude, unlink and delete the links.
  * Saves the page with edited links.
  * expected in 0.4.0: Ability to import images.

## Installation ##
Place the contents of this directory in the extensions/EditLinks/-directory of your MediaWiki installation. Requires [scary transclusion](http://www.mediawiki.org/wiki/Manual:$wgEnableScaryTranscluding) to be enabled for the transcluding option to work.

Add the following line to your LocalSettings.php:
```
require_once ("$IP/extensions/EditLinks/EditLinks.php");
```

## User rights ##
|import-wizard|Allows a user with that right set to use the edit links extension.|
|:------------|:-----------------------------------------------------------------|
