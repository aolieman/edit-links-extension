=Edit links=
A MediaWiki extension created by Alex Olieman and Roel Obdam.

Current version: 0.3.2
Last update: 2009-30-07

This extension is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 2 of the License.

The Edit links extension adds extra functionality to MediaWiki for editing links on local pages. It was written to complement the Import Wizard extention by Raymond Jelierse.

Enter the name of a (previously imported) local page of which you want to edit the links. Select an external wiki for interwiki transclusion of link targets. You will have options to transclude, unlink and delete the links and to upload missing images.

==Features==
(* Import an article from a specified list of sources, using the import wizard.)
* Use the imported page as source for Edit links.
* It generates an editable list of all internal links on the page.
* Offers checkboxes to transclude, unlink and delete the links.
* Saves the page with edited links.
* [expected 0.4.0] Ability to import images.

==Installation==
Place the contents of this directory in the extensions/EditLinks/-directory of your MediaWiki installation.
Add the following line to your LocalSettings.php:
    require_once ("$IP/extensions/EditLinks/EditLinks.php");
