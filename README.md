oclife
======

Tagging and getting extended document informations on ownCloud.

IMPORTANT NOTE: This project is in development stage and should not be used in production environment and / or with important data. Nor the author nor any other thirty party can be responsible for any damage or data loss.

=About this document=
This document has been written by Francesco Piraneo G. the author of oclife referring to the version committed on February 23 2014. Contained informations may not apply to future releases of oclife. I’m an Italian mother tongue so I’m very sorry for any mistake made editing this document: Please feel free to submit improvements.

=About=
oclife allow an ownCloud user to manage and add hierarchical tags to his document. One or more tag can be added to the document to better identify his property; I personally plan to use it extensively to add tags to the thousands of pictures I took since I buy my first digicam: Since Y2K (year where I bought my Nikon Coolpix 995) I took pictures that I collected into a directory structure of my PC first and then on my server and NAS. I was satisfied of this arrangement since I realized that storing then successive looking for a particular picture was not so trivial; for a picture taken in Russia and depicting one person for example: Should I store that picture under `/NAS/Russia` or under `/NAS/PersonName`? And what are pictures I took in Canada? I’d like to see like on a light board all the pictures taken in Canada to look for a particular one!

Extending this concept for other kind of document like MP3 is automatic: What are all my stored audio files containing all birds sound I collected in 2008?

A similar concept has been implemented in `phTagr`() by …, a product that I love but it cannot actually be extended to other kind of document than pictures and I’ve found it hard to expand it to support other kind of picture format (like digicam’s RAW and old Kodak PhotoCD).

==What is it==
oclife allows to declare all the tags under the main menu entrance `Tags`, allowing basic editing like “Rename”, “Delete” and with a simple drag and drop putting them in a hierarchical tree. On the main tags editing page, simply selecting one or more tag(s) the app shows all the documents with that tag(s). Clicking on a document’s thumbnail the ownCloud path of that document is shown.

Tags can be assigned to the document using the `Info` window that can be recalled on the main file page, browsing through all the file: Simply hover with the mouse on a document to show the related operations allowed on that document. The information window also produces an enlarged thumbnail supporting other file formats than the standard ownCloud libraries.

===The concept of hierarchical===
Few words on the concept of hierarchy: As above I plan to use this product with pictures but the concept can be extensively used on other kind of documents. Consider pictures taken in Great Britain’s region like `Wales`, `England` and `Scotland`; each of them has been taken in `London`, `Manchester`, `Wrexham` and `Edinburgh`. We have the choice - for example - for a picture taken in `London` to assign the `London` tag but for a successive search for all the pictures taken in United Kingdom, the picture taken in `London` doesn’t appears, compelling us to insert three tags: `London`, `England` and `United Kingdom` ... may be also ... `Europe`?  

Simply declaring a tag structure like the following can solve the issue:

Europe
+-United Kingdom
  +-England
    +-London
  +-Scotland
    +-Edinburgh
  +-Wales
    +-Wrexham

On tag window I can choose the `United Kingdom` tag to let all the pictures taken in `London`, `Wrexham` and `Edinburgh` appears.

==What is it not==
oclife actually doesn’t incorporate any file viewer or slideshow; it has not been tested on shared documents. Some limitations apply if files are encrypted. Clicking on a thumbnail on the main `Tags` window a popup appears showing the path, not the document.

=Prerequisites=
==ownCloud==
oclife has been developed for and tested under ownCloud 6.0; use of oclife under different version of ownCloud may lead to unpredictable results. Despite ownCloud support different RDBMs, oclife has been tested only under MySQL. Feel free to test it with other RDBMs and share your experience.

oclife has been developed with a MacBook Pro running MacOS X Maverick. ownCloud doesn’t support MacOS as host OS due to unicode issues.

Full MAMP setup:
* MacOS X 10.9.1
* Apache 2.2.24
* PHP 5.4.17
* MySQL 5.6.14
* ImageMagick 6.8.8 with php module v. 3.1.2.

oclife has been tested also on servers running Debian Linux.

==Thirty party==
oclife uses thirty party modules and library to perform his job; standard modules delivered with ownCloud (like jQuery) will not be listed here.

* FancyTree - Creates a nice tree view of all the tags and allow extra features like context menu and drag and drop;
* bootstrap-tokenfield - Allows a nice tags show on the info popup with insert of new tag feature.

Both modules can be downloaded on the developer’s page at GitHub; for sake of simplicity they will be distributed with oclife leaving untouched the original license and credits.

=Installation=

=How to use=

=Future developments=

==Localizations==
==Multiple languages tags==
==Direct access to a file==
==Sharing by tags==
==Multiple hierarchy==
==Personal vs. Global tags==

==Known issues==
===ImageMagick vs. GD===
===Encrypted files===
===Rename a file===

=Special thanks to=

