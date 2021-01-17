# PHP Mail Form

Free PHP Mail Form v2.4.6 - Secure single-page PHP mail form for your website
Copyright (C) Jem Turner 2007-2021.
https://jemsmailform.com/

## Thank you

Thank you for downloading Jem's Free PHP Mail Form. If you like it please consider upgrading to premium:
https://jemsmailform.com/premium/

## Updates
This fork has been created by Adam Dimech
https://www.adonline.id.au/

Adam Dimech's changes to the code base:
- The form will not allow users with JavaScript disabled to submit the form. This may impact 1% of legitimate internet users but more than 90% of spammers.
- The webpage code is changed from XHTML to HTML.
- The file name is changed to contact.php and instructions updated.
- Minimal CSS styling has been changed to incorporate the use of `flexbox`/`grid` and other modern CSS. Styling is included in the HTML header but could be moved to a separate CSS file.
- A few more spam words were added to the list of offensive terms.

## Instructions

To get started with the script, open contact.php in your favourite plain text editor - this might be something like Microsoft Notepad, or Mac TextEdit. Do not open the file in Microsoft Word or programs of that sort, because it will mess up the special characters.

When you've opened the file, customise the options at the top of the file E.g. to set the email address you'd like to receive email at, change
`$yourEmail = "";` to `$yourEmail = "your_email@yourwebsite.com"`; replacing `your_email@yourwebsite.com` with your email address.

When you've finished customising the options, save the file.

## Licensing

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

To read the GNU General Public License, see http://www.gnu.org/licenses/.
