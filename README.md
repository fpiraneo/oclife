oclife
======
**IMPORTANT NOTE:** This project is in development stage and should not be used in production environment and / or with important data. Nor the author nor any other thirty party can be responsible for any damage or data loss.

#About this document
This document has been written by Francesco Piraneo G. the author of oclife referring to the version committed on February 23 2014. Contained informations may not apply to future releases of oclife. I’m an Italian mother tongue so I’m very sorry for any mistake made editing this document: Please feel free to submit improvements.

#About
oclife allow an ownCloud user to manage and add hierarchical tags to his document. One or more tag can be added to the document to better identify his property; I personally plan to use it extensively to add tags to the thousands of pictures I took since I buy my first digicam: Since Y2K (year where I bought my Nikon Coolpix 995) I took pictures that I collected into a directory structure of my PC first and then on my server and NAS. I was satisfied of this arrangement since I realized that storing then successive looking for a particular picture was not so trivial; for a picture taken in Russia and depicting one person for example: Should I store that picture under `/NAS/Russia` or under `/NAS/PersonName`? And what are pictures I took in Canada? I’d like to see like on a light board all the pictures taken in Canada to look for a particular one!

Extending this concept for other kind of document like MP3 is automatic: What are all my stored audio files containing all birds sound I collected in 2008?

A similar concept has been implemented in `phTagr`() by …, a product that I love but it cannot actually be extended to other kind of document than pictures and I’ve found it hard to expand it to support other kind of picture format (like digicam’s RAW and old Kodak PhotoCD).

##What is it
oclife allows to declare all the tags under the main menu entrance `Tags`, allowing basic editing like “Rename”, “Delete” and with a simple drag and drop putting them in a hierarchical tree. On the main tags editing page, simply selecting one or more tag(s) the app shows all the documents with that tag(s). Clicking on a document’s thumbnail the ownCloud path of that document is shown.

Tags can be assigned to the document using the `Info` window that can be recalled on the main file page, browsing through all the file: Simply hover with the mouse on a document to show the related operations allowed on that document. The information window also produces an enlarged thumbnail supporting other file formats than the standard ownCloud libraries.

###The concept of hierarchical
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

##What is it not
oclife actually doesn’t incorporate any file viewer or slideshow; it has not been tested on shared documents. Some limitations apply if files are encrypted. Clicking on a thumbnail on the main `Tags` window a popup appears showing the path, not the document.

#Prerequisites
##ownCloud
oclife has been developed for and tested under ownCloud 6.0; use of oclife under different version of ownCloud may lead to unpredictable results. Despite ownCloud support different RDBMs, oclife has been tested only under MySQL. Feel free to test it with other RDBMs and share your experience.

oclife has been developed with a MacBook Pro running MacOS X Maverick. ownCloud doesn’t support MacOS as host OS due to unicode issues.

Full MAMP setup:
* MacOS X 10.9.1
* Apache 2.2.24
* PHP 5.4.17
* MySQL 5.6.14
* ImageMagick 6.8.8 with php module v. 3.1.2.

oclife has been tested also on servers running Debian Linux.

##Thirty party
oclife uses thirty party modules and library to perform his job; standard modules delivered with ownCloud (like jQuery) will not be listed here.

* FancyTree - Creates a nice tree view of all the tags and allow extra features like context menu and drag and drop;
* bootstrap-tokenfield - Allows a nice tags show on the info popup with insert of new tag feature.

Both modules can be downloaded on the developer’s page at GitHub; for sake of simplicity they will be distributed with oclife leaving untouched the original license and credits.

##ImageMagick vs. GD
oclife can be used with standard GD graphic library integrated in PHP; GD can handle a limitate number of graphical image format; ImageMagic allows more flexibility but it must be installed manually. Once ImageMagic is installed, it will be sensed automatically by oclife and the usage should be activated manually on the `admin` page by an administrator.

#Installation
oclife can be downloaded from github (hitting "Download ZIP" pushbutton) then installed under your ownCloud installation directory then the `apps` subdirectory.

Note: The `.../appinfo/info.xml` contains some behaviour of oclife under ownCloud. The `shipped` property of oclife has been set to `TRUE`; this allows the oclife directory to don't be removed when the app is disabled on the Administrator's app page; I think is more practical to leave the inhibited app on the app directory also if they are not enabled.

After unzipping the downloaded archive under the `apps` directory of ownCloud, the app has to be activated; login on ownCloud as `administrator` (note: the actual administrator's username can be different following your installation), in the lower left corner of the page you can find the `apps` menu icon; recall it then browse the app listing to look for `oclife`; select and enable it.

#How to use
On the main page a new `Tags` icon on the left menu bar should appears; select it; a single item called `root` is displayed: That is the root tag, parent of all the tags you will create.

A right click on the `root` tag shows the contextual menu; choose `New` to enter a new tag.
As you can see there are other two options like `Rename` and `Delete` which the meanings and usage should be trivial.

**Note:** Of course you cannot rename or delete the `root` tag!

A small note about the icon of each tag: the `globe`. This icon means that the tag is `global`: A global tag (as the name implies) is visible to all the users of that installation of ownCloud! In a near future I'll implements also personal tags (with different icon of course!).

There is also an alternative way to create tags.
Browsing the files listing and hoovering over a file, the actions icons will appears; you note a new icon called `Informations`; clicking on it opens a popup with basic informations on that file and a tagbox on the bottom; insert there the tag you like to create and hit enter; a popup asking for confirmation appears: confirm and the tag will be created.

#Future developments
Again, this is just the first release of the app and features can be added or improved.

##Localizations
Actually only the English language is implemented; the localization infrastructure will be added in the next future; I can provide localization file for Italian and French; for other languages feels free to send me your contribute.

##Multiple languages tags
I'm a Swiss resident; in Switzerland we currently use four languages plus English; the localization not only of the application but also of the data is a must. The infrastructure to add a single tag with multiple language is present; it need to be handled by the logic.

##Direct access to a file
On the main tag window, clicking on a thumbnail, a popup with the ownCloud path of the file appears, stating where user can find the indicated file. The recall of the `File` app standard functionality (like download or show PDF for PDF file) should be nice; unfortunately I can't understand how I can integrate this feature in the ownCloud infrastructure. **HELP WANTED!!! :-)**

##Sharing by tags
Allows the user to share a link with a token indicating a slideshow or an album with files with such tags. To be evaluated.

##Multiple hierarchy
Tags with multiple parents; consider the files related to `Blaise Pascal` that can be a child of `Scientists` and of `French personalities`; both are corrects and on large archives can be a great added value.

##Personal vs. Global tags
All tags are actually **global**, this means that all the users of one installation of ownCloud can see all the tags; personal tags allows to define tags related to a single user that cannot be seen by others.

##Known issues
###Encrypted files
Not all functionality works with encrypted files, i.e. image rotations, EXIF data readings and so on. We suggest to keep the data unencrypted.

###Rename a file
After renaming a file the *work in progress* is shown forever; my suspect is trouble in the ownCloud infrastructures to handle the *file rename* hook.

#Special thanks to
This app has been written in my spare time and on a train between Bellinzona and Mezzovico, on the path between my house and the company where I work.
My first thank is for my wife - Thank you very much for her patience! It's not an easy task to live with a software engineer.

Special thanks to the people that wrote ownCloud: The code is clear, well commented and easy to understand; try to read the MediaWiki code and you'll understand me! ;-)

Last but not least thanks to *ragulka*, the author of `bootstrap-tokenfield`; his help was precious while debugging!
>>>>>>> e8613fa9c5c11baab74b4a3b97253ee00d5e4085
